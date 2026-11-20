<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            'Одежда',
            'Техника',
            'Косметика',
            'Книги',
            'Дом',
            'Хобби',
            'Другое',
        ])->mapWithKeys(fn (string $name) => [
            $name => Category::query()->create(['name' => $name]),
        ]);

        $people = [
            [
                'name' => 'Аня',
                'username' => 'anna',
                'email' => 'anna@miuwish.test',
                'age' => 22,
                'bio' => 'Собираю список к дню рождения. Можно смотреть и бронировать без предупреждения.',
                'color' => '#E8A9BC',
            ],
            [
                'name' => 'Миша',
                'username' => 'misha',
                'email' => 'misha@miuwish.test',
                'age' => 24,
                'bio' => 'Люблю технику и настольные игры. Сюрприз важнее, чем сам подарок.',
                'color' => '#EFC4D2',
            ],
            [
                'name' => 'Лина',
                'username' => 'lina',
                'email' => 'lina@miuwish.test',
                'age' => 21,
                'bio' => 'Книги, косметика и всё, что делает дом уютнее.',
                'color' => '#F6D8E1',
            ],
            [
                'name' => 'Костя',
                'username' => 'kostya',
                'email' => 'kostya@miuwish.test',
                'age' => 23,
                'bio' => 'Список к Новому году. Если желание занято, лучше выбрать другое.',
                'color' => '#8C7D82',
            ],
        ];

        $users = [];
        foreach ($people as $person) {
            $users[$person['username']] = User::query()->create([
                'name' => $person['name'],
                'username' => $person['username'],
                'email' => $person['email'],
                'password' => 'password',
                'age' => $person['age'],
                'bio' => $person['bio'],
                'avatar' => $this->picture('avatars', $person['name'], $person['color']),
            ]);
        }

        $items = [
            ['anna', 'Тёплое пальто', 'Одежда', 8900, 'Пыльно-розовое, размер M.', 'https://example.com/coat'],
            ['anna', 'Наушники', 'Техника', 4500, 'Любые удобные, с шумоподавлением.', 'https://example.com/headphones'],
            ['anna', 'Набор кистей', 'Хобби', 1200, 'Для акварели, мягкие.', null],
            ['misha', 'Механическая клавиатура', 'Техника', 7200, 'Тихие переключатели.', 'https://example.com/keyboard'],
            ['misha', 'Настольная игра', 'Хобби', 2500, 'На компанию от четырёх человек.', 'https://example.com/boardgame'],
            ['misha', 'Кружка', 'Дом', 800, 'Большая, без принта.', null],
            ['lina', 'Роман в мягкой обложке', 'Книги', 650, 'Что-нибудь новое, не классика.', 'https://example.com/book'],
            ['lina', 'Крем для рук', 'Косметика', 900, 'Без сильного запаха.', 'https://example.com/cream'],
            ['lina', 'Плед', 'Дом', 2100, 'Светлый, не очень толстый.', null],
            ['kostya', 'Фонарик', 'Другое', 1500, 'Карманный, с зарядкой от USB.', 'https://example.com/light'],
            ['kostya', 'Рюкзак', 'Одежда', 3900, 'Для города, на 20 литров.', 'https://example.com/bag'],
            ['kostya', 'Блокнот', 'Книги', 400, 'В точку, формат A5.', null],
        ];

        $wishes = [];
        foreach ($items as [$owner, $title, $category, $price, $description, $url]) {
            $wishes[$title] = Wishlist::query()->create([
                'user_id' => $users[$owner]->id,
                'category_id' => $categories[$category]->id,
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'url' => $url,
                'image' => $this->picture('wishes', $title, '#E8A9BC'),
                'status' => Wishlist::AVAILABLE,
            ]);
        }

        $bookings = [
            ['misha', 'Тёплое пальто'],
            ['lina', 'Механическая клавиатура'],
            ['anna', 'Фонарик'],
        ];

        foreach ($bookings as [$booker, $title]) {
            $wish = $wishes[$title];
            $wish->reservation()->create([
                'user_id' => $users[$booker]->id,
            ]);
            $wish->update(['status' => Wishlist::RESERVED]);
        }
    }

    private function picture(string $directory, string $label, string $hex): string
    {
        $image = imagecreatetruecolor(800, 600);
        [$red, $green, $blue] = sscanf($hex, '#%02x%02x%02x');
        $background = imagecolorallocate($image, $red, $green, $blue);
        imagefilledrectangle($image, 0, 0, 800, 600, $background);
        $ink = imagecolorallocate($image, 59, 51, 54);
        imagestring($image, 5, 24, 280, 'MiuWish', $ink);

        $name = $directory.'/'.str()->uuid().'.jpg';
        Storage::disk('public')->makeDirectory($directory);
        imagejpeg($image, Storage::disk('public')->path($name), 82);
        imagedestroy($image);

        return $name;
    }
}
