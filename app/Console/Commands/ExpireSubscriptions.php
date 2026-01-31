<?php

namespace App\Console\Commands;

use App\Contracts\Services\SubscriptionServiceInterface;
use App\Domain\ValueObjects\SubscriptionId;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription as SubscriptionModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire
                            {--dry-run : Показать подписки, которые будут обработаны, без изменения статуса}
                            {--limit= : Ограничить количество обрабатываемых подписок}';

    protected $description = 'Обработка истекших подписок: обновление статуса на expired '
        . 'для подписок с истекшим сроком действия';

    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Обработка истекших подписок...');

        $isDryRun = $this->option('dry-run');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        if ($isDryRun) {
            $this->warn('[DRY RUN MODE] Подписки не будут изменены');
        }

        /** @var \Illuminate\Database\Eloquent\Builder<SubscriptionModel> $query */
        $query = SubscriptionModel::query()
            ->where('status', SubscriptionStatus::Active)
            ->where('current_period_end', '<', Carbon::now())
            ->whereNotNull('current_period_end');

        $totalCount = $query->count();

        if ($totalCount === 0) {
            $this->info('Нет истекших подписок для обработки.');
            return Command::SUCCESS;
        }

        $this->info("Найдено подписок для обработки: {$totalCount}");

        if ($limit) {
            $query->limit($limit);
            $this->info("Обрабатывается первые {$limit} подписок...");
        }

        $models = $query->with(['course', 'user'])->get();
        $processedCount = 0;
        $errorCount = 0;

        foreach ($models as $model) {
            try {
                if (!$isDryRun) {
                    $this->subscriptionService->expireSubscription(
                        new SubscriptionId($model->id)
                    );
                }

                $courseTitle = $model->course?->title ?? 'не указано';
                $userEmail = $model->user?->email ?? 'не указано';
                $expiredDate = ($model->current_period_end !== null)
                    ? $model->current_period_end->format('Y-m-d')
                    : 'не указано';

                $statusMark = $isDryRun ? "[БУДЕТ ОБНОВЛЕНО]" : "[ОБНОВЛЕНО]";
                $this->line("  {$statusMark} Подписка #{$model->id} " .
                    ($isDryRun ? "будет обновлена" : "обновлена") .
                    " (Курс: \"{$courseTitle}\", " .
                    "Пользователь: {$userEmail}, " .
                    "Истекла: {$expiredDate})");

                $processedCount++;
            } catch (Exception $e) {
                $this->error("  [ОШИБКА] Подписка #{$model->id}: " . $e->getMessage());
                $errorCount++;
            }
        }

        if ($isDryRun) {
            $this->warn("\nПодписки не были изменены (режим dry-run)");
        } else {
            $this->info("\nОбработано: {$processedCount} подписок");
            if ($errorCount > 0) {
                $this->warn("Ошибок: {$errorCount}");
            }
        }

        $this->info('Команда выполнена успешно!');
        return Command::SUCCESS;
    }
}
