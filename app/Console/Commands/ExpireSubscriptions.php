<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
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

    public function handle(): int
    {
        $this->info('Обработка истекших подписок...');

        $isDryRun = $this->option('dry-run');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        if ($isDryRun) {
            $this->warn('[DRY RUN MODE] Подписки не будут изменены');
        }

        /** @var \Illuminate\Database\Eloquent\Builder<Subscription> $query */
        $query = Subscription::query()
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

        /** @var \Illuminate\Database\Eloquent\Collection<int, Subscription> $subscriptions */
        $subscriptions = $query->with(['course', 'user'])->get();
        $processedCount = 0;
        $errorCount = 0;

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            try {
                if (!$isDryRun) {
                    $subscription->update([
                        'status' => SubscriptionStatus::Expired,
                    ]);
                }

                $courseTitle = $subscription->course?->title ?? 'не указано';
                $userEmail = $subscription->user?->email ?? 'не указано';
                $expiredDate = $subscription->current_period_end?->format('Y-m-d') ?? 'не указано';

                $statusMark = $isDryRun ? "[БУДЕТ ОБНОВЛЕНО]" : "[ОБНОВЛЕНО]";
                $this->line("  {$statusMark} Подписка #{$subscription->id} " .
                    ($isDryRun ? "будет обновлена" : "обновлена") .
                    " (Курс: \"{$courseTitle}\", " .
                    "Пользователь: {$userEmail}, " .
                    "Истекла: {$expiredDate})");

                $processedCount++;
            } catch (Exception $e) {
                $this->error("  [ОШИБКА] Подписка #{$subscription->id}: " . $e->getMessage());
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
