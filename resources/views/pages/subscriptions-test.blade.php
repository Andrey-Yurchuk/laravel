@extends('layouts.app')

@section('title', 'Тестирование подписок')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-3xl font-bold mb-2">Тестирование агрегата Subscription</h1>
    <p class="text-gray-600 mb-6">Эта страница позволяет протестировать работу агрегата Subscription</p>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h5 class="text-lg font-semibold mb-4">1. Получить список подписок</h5>
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded" onclick="loadSubscriptions()">Загрузить подписки</button>
                <div id="subscriptions-result" class="mt-3"></div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h5 class="text-lg font-semibold mb-4">2. Создать подписку</h5>
                <form id="create-subscription-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Course ID</label>
                        <input type="number" class="w-full border border-gray-300 rounded px-3 py-2" name="course_id" value="1" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan ID</label>
                        <input type="number" class="w-full border border-gray-300 rounded px-3 py-2" name="plan_id" value="1" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period Start</label>
                        <input type="date" class="w-full border border-gray-300 rounded px-3 py-2" name="period_start" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period End</label>
                        <input type="date" class="w-full border border-gray-300 rounded px-3 py-2" name="period_end" required>
                    </div>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded w-full">Создать подписку</button>
                </form>
                <div id="create-result" class="mt-3"></div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h5 class="text-lg font-semibold mb-4">3. Активировать подписку</h5>
                <form id="activate-subscription-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subscription ID</label>
                        <input type="number" class="w-full border border-gray-300 rounded px-3 py-2" name="id" required>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full font-semibold">Активировать</button>
                </form>
                <div id="activate-result" class="mt-3"></div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h5 class="text-lg font-semibold mb-4">4. Отменить подписку</h5>
                <form id="cancel-subscription-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subscription ID</label>
                        <input type="number" class="w-full border border-gray-300 rounded px-3 py-2" name="id" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Причина отмены</label>
                        <textarea class="w-full border border-gray-300 rounded px-3 py-2" name="reason" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full font-semibold">Отменить</button>
                </form>
                <div id="cancel-result" class="mt-3"></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h5 class="text-lg font-semibold mb-4">Лог операций</h5>
        <div id="log" class="bg-gray-50 rounded p-4 max-h-64 overflow-y-auto font-mono text-sm"></div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function log(message, type = 'info') {
    const logDiv = document.getElementById('log');
    const time = new Date().toLocaleTimeString();
    const colors = {
        info: 'text-blue-600',
        success: 'text-green-600',
        error: 'text-red-600',
        warning: 'text-yellow-600'
    };
    logDiv.innerHTML += `<div class="${colors[type]}">[${time}] ${message}</div>`;
    logDiv.scrollTop = logDiv.scrollHeight;
}

function showResult(elementId, data, isError = false) {
    const element = document.getElementById(elementId);
    const bgColor = isError ? 'bg-red-50' : 'bg-gray-50';
    element.innerHTML = `<pre class="${bgColor} p-3 rounded text-xs overflow-x-auto">${JSON.stringify(data, null, 2)}</pre>`;
    log(`${isError ? 'Ошибка' : 'Успех'}: ${JSON.stringify(data)}`, isError ? 'error' : 'success');
}

async function loadSubscriptions() {
    try {
        log('Загрузка подписок...', 'info');
        const response = await fetch('/api/subscriptions');
        const data = await response.json();
        showResult('subscriptions-result', data);
    } catch (error) {
        showResult('subscriptions-result', {error: error.message}, true);
    }
}

document.getElementById('create-subscription-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        course_id: parseInt(formData.get('course_id')),
        plan_id: parseInt(formData.get('plan_id')),
        period_start: formData.get('period_start'),
        period_end: formData.get('period_end')
    };

    try {
        log('Создание подписки...', 'info');
        const response = await fetch('/api/subscriptions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        showResult('create-result', result, !response.ok);
    } catch (error) {
        showResult('create-result', {error: error.message}, true);
    }
});

document.getElementById('activate-subscription-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const id = formData.get('id');

    try {
        log(`Активация подписки #${id}...`, 'info');
        const response = await fetch(`/api/subscriptions/${id}/activate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const result = await response.json();
        showResult('activate-result', result, !response.ok);
    } catch (error) {
        showResult('activate-result', {error: error.message}, true);
    }
});

document.getElementById('cancel-subscription-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
        id: formData.get('id'),
        reason: formData.get('reason')
    };

    try {
        log(`Отмена подписки #${data.id}...`, 'info');
        const response = await fetch(`/api/subscriptions/${data.id}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({reason: data.reason})
        });
        const result = await response.json();
        showResult('cancel-result', result, !response.ok);
    } catch (error) {
        showResult('cancel-result', {error: error.message}, true);
    }
});

document.querySelector('input[name="period_start"]').valueAsDate = new Date();
const endDate = new Date();
endDate.setMonth(endDate.getMonth() + 1);
document.querySelector('input[name="period_end"]').valueAsDate = endDate;
</script>
@endsection
