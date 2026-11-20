<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_asked_to_log_in_before_booking(): void
    {
        $wishlist = $this->wishlist();

        $this->get(route('reservations.create', $wishlist))
            ->assertRedirect(route('login'));
    }

    public function test_user_cannot_book_their_own_wish(): void
    {
        $owner = User::factory()->create();
        $wishlist = $this->wishlist($owner);

        $this->actingAs($owner)
            ->post(route('reservations.store', $wishlist))
            ->assertRedirect(route('wishes.show', $wishlist))
            ->assertSessionHasErrors('wishlist');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_second_booking_is_rejected_and_owner_does_not_see_the_booker(): void
    {
        $owner = User::factory()->create(['name' => 'Owner Person']);
        $first = User::factory()->create(['name' => 'First Booker']);
        $second = User::factory()->create(['name' => 'Second Booker']);
        $wishlist = $this->wishlist($owner);

        $this->actingAs($first)
            ->post(route('reservations.store', $wishlist))
            ->assertRedirect(route('reservations.index'));

        $this->actingAs($first)
            ->post(route('reservations.store', $wishlist))
            ->assertRedirect(route('reservations.index'))
            ->assertSessionHasNoErrors();

        $this->actingAs($second)
            ->from(route('reservations.create', $wishlist))
            ->post(route('reservations.store', $wishlist))
            ->assertRedirect(route('reservations.create', $wishlist))
            ->assertSessionHasErrors('wishlist');

        $this->assertDatabaseCount('reservations', 1);

        $this->actingAs($second)
            ->get(route('reservations.create', $wishlist))
            ->assertRedirect(route('wishes.show', $wishlist))
            ->assertSessionHasNoErrors();

        $this->actingAs($owner)
            ->get(route('wishes.show', $wishlist))
            ->assertOk()
            ->assertDontSee('Забронировано')
            ->assertDontSee('Доступно')
            ->assertDontSee('First Booker')
            ->assertDontSee('Second Booker');

        $this->actingAs($owner)
            ->get(route('wishes.index'))
            ->assertOk()
            ->assertDontSee('Забронировано')
            ->assertDontSee('Доступно');

        $this->actingAs($owner)
            ->get(route('profiles.show', $owner))
            ->assertOk()
            ->assertDontSee('Забронировано');

        auth()->logout();

        $this->get(route('wishes.show', $wishlist))
            ->assertOk()
            ->assertSee('Забронировано')
            ->assertDontSee('First Booker');

        $this->get(route('profiles.show', $owner))
            ->assertOk()
            ->assertSee('Забронировано')
            ->assertDontSee('First Booker');
    }

    public function test_booker_can_cancel_and_the_wish_becomes_available(): void
    {
        $owner = User::factory()->create();
        $booker = User::factory()->create();
        $wishlist = $this->wishlist($owner);

        $this->actingAs($booker)->post(route('reservations.store', $wishlist));

        $reservation = $wishlist->reservation()->first();

        $this->actingAs($booker)
            ->delete(route('reservations.destroy', $reservation))
            ->assertRedirect(route('reservations.index'));

        $this->assertDatabaseCount('reservations', 0);
        $this->assertSame(Wishlist::AVAILABLE, $wishlist->fresh()->status);
    }

    public function test_owner_filters_wishes_by_category(): void
    {
        $owner = User::factory()->create();
        $books = Category::query()->create(['name' => 'Книги']);
        $tech = Category::query()->create(['name' => 'Техника']);
        $this->wishlist($owner, $books, 'Роман');
        $this->wishlist($owner, $tech, 'Наушники');

        $this->actingAs($owner)
            ->get(route('wishes.index', ['category' => $books->id]))
            ->assertOk()
            ->assertSee('Роман')
            ->assertDontSee('Наушники');
    }

    public function test_user_can_add_a_wish_with_an_image(): void
    {
        $owner = User::factory()->create();
        $category = Category::query()->create(['name' => 'Дом']);

        $this->actingAs($owner)->post(route('wishes.store'), [
            'title' => 'Плед',
            'price' => 1500,
            'category_id' => $category->id,
            'description' => 'Светлый',
            'image' => UploadedFile::fake()->image('blanket.jpg', 1600, 800),
        ])->assertRedirect();

        $wishlist = Wishlist::query()->where('title', 'Плед')->first();
        $this->assertNotNull($wishlist);
        $this->assertNotNull($wishlist->image);
        $size = getimagesize(storage_path('app/public/'.$wishlist->image));
        $this->assertLessThanOrEqual(1200, $size[0]);
        $this->assertSame($size[0] / $size[1], 1200 / 600);

        $this->actingAs($owner)
            ->get(route('wishes.show', $wishlist))
            ->assertOk()
            ->assertSee('h-auto w-full', false)
            ->assertDontSee('h-72 w-full object-cover', false)
            ->assertDontSee('h-44 w-full object-cover', false);
    }

    public function test_booker_sees_their_reservation_without_revealing_their_name_to_the_owner(): void
    {
        $owner = User::factory()->create(['name' => 'Owner Person']);
        $booker = User::factory()->create(['name' => 'Quiet Booker']);
        $wishlist = $this->wishlist($owner);

        $this->actingAs($booker)->post(route('reservations.store', $wishlist));

        $this->actingAs($booker)
            ->get(route('wishes.show', $wishlist))
            ->assertOk()
            ->assertSee('Забронировано')
            ->assertSee('Вы забронировали это желание');

        $this->actingAs($owner)
            ->get(route('wishes.show', $wishlist))
            ->assertOk()
            ->assertDontSee('Забронировано')
            ->assertDontSee('Вы забронировали')
            ->assertDontSee('Quiet Booker');
    }

    public function test_deleting_the_booker_returns_the_wish_to_others(): void
    {
        $owner = User::factory()->create();
        $booker = User::factory()->create();
        $wishlist = $this->wishlist($owner);

        $this->actingAs($booker)->post(route('reservations.store', $wishlist));
        $booker->delete();

        $this->assertSame(Wishlist::AVAILABLE, $wishlist->fresh()->status);
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_shop_link_must_be_a_web_address(): void
    {
        $owner = User::factory()->create();
        $category = Category::query()->create(['name' => 'Книги '.$owner->id]);

        $this->actingAs($owner)
            ->from(route('wishes.create'))
            ->post(route('wishes.store'), [
                'title' => 'Книга',
                'price' => 500,
                'category_id' => $category->id,
                'url' => 'javascript:alert(1)',
            ])
            ->assertRedirect(route('wishes.create'))
            ->assertSessionHasErrors('url');
    }

    private function wishlist(?User $owner = null, ?Category $category = null, string $title = 'Пальто'): Wishlist
    {
        $owner ??= User::factory()->create();
        $category ??= Category::query()->create(['name' => 'Одежда '.$owner->id]);

        return Wishlist::query()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'title' => $title,
            'price' => 1000,
            'status' => Wishlist::AVAILABLE,
        ]);
    }
}
