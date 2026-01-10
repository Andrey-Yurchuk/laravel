<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Курс создан</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .detail-item {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #6b7280;
            display: inline-block;
            width: 150px;
        }
        .detail-value {
            color: #111827;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Поздравляем! Ваш курс успешно создан</h1>
    </div>
    
    <div class="content">
        <p>Здравствуйте, <strong>{{ $course->instructor->name }}</strong>!</p>
        
        <p>Ваш курс <strong>{{ $course->title }}</strong> был успешно создан в системе EduFlow.</p>
        
        <div class="details">
            <h2 style="margin-top: 0; color: #111827;">Детали курса:</h2>
            
            <div class="detail-item">
                <span class="detail-label">Название:</span>
                <span class="detail-value">{{ $course->title }}</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Категория:</span>
                <span class="detail-value">{{ $course->category->name }}</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Уровень сложности:</span>
                <span class="detail-value">{{ $course->difficulty_level->value }}</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Статус:</span>
                <span class="detail-value">{{ $course->status->value }}</span>
            </div>
            
            @if($course->description)
            <div class="detail-item">
                <span class="detail-label">Описание:</span>
                <span class="detail-value">{{ $course->description }}</span>
            </div>
            @endif
        </div>
        
        <p>Теперь вы можете:</p>
        <ul>
            <li>Добавить уроки к вашему курсу</li>
            <li>Настроить планы подписки</li>
            <li>Опубликовать курс для студентов</li>
        </ul>
        
        <p style="margin-top: 30px;">
            <a href="{{ config('app.url') }}/instructor/courses" class="button">Управление курсами</a>
        </p>
    </div>
    
    <div class="footer">
        <p>С уважением,<br><strong>Команда EduFlow</strong></p>
        <p style="font-size: 12px; margin-top: 20px;">
            Это автоматическое уведомление. Пожалуйста, не отвечайте на это письмо.
        </p>
    </div>
</body>
</html>

