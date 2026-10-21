<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiuWish - Панель управления</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { flex: 1; background: #e1f0fa; padding: 20px; border-radius: 8px; text-align: center; border: 1px solid #bce0fd; }
        .stat-card h3 { margin: 0; font-size: 14px; color: #2980b9; text-transform: uppercase; }
        .stat-card p { margin: 10px 0 0; font-size: 28px; font-weight: bold; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #495057; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-free { background-color: #d4edda; color: #155724; }
        .badge-booked { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Панель управления MiuWish</h1>
        
        <!-- Блок со статистикой -->
        <div class="stats">
            <div class="stat-card">
                <h3>Пользователи</h3>
                <p>{{ $stats['users'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Желания</h3>
                <p>{{ $stats['wishes'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Бронирования</h3>
                <p>{{ $stats['bookings'] }}</p>
            </div>
        </div>

        <!-- Таблица с последними желаниями -->
        <h2>Последние добавленные желания</h2>
        <table>
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Цена</th>
                    <th>Категория</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentWishes as $wish)
                    <tr>
                        <td>{{ $wish->title }}</td>
                        <td>{{ $wish->price }} руб.</td>
                        <td>{{ $wish->category }}</td>
                        <td>
                            @if($wish->booking)
                                <span class="badge badge-booked">Забронировано</span>
                            @else
                                <span class="badge badge-free">Свободно</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>