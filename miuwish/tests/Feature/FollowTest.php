<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_login(): void
    {
        $this->get(route('friends.index'))->assertRedirect(route('login'));
    }

    public function test_empty_search_does_not_list_everyone(): void
    {
        $me = User::factory()->create(['name' => 'Анна']);
        User::factory()->create(['name' => 'Михаил']);

        $this->actingAs($me)
            ->get(route('friends.index'))
            ->assertOk()
            ->assertDontSee('Михаил');
    }

    public function test_search_by_name_and_follow_shows_both_lists(): void
    {
        $me = User::factory()->create(['name' => 'Анна Иванова']);
        $friend = User::factory()->create(['name' => 'Михаил Петров']);
        User::factory()->create(['name' => 'Костя']);

        $this->actingAs($me)
            ->get(route('friends.index', ['q' => 'Михаил']))
            ->assertOk()
            ->assertSee('Михаил Петров')
            ->assertDontSee('Костя')
            ->assertSee('Подписаться');

        $this->actingAs($me)
            ->post(route('friends.store', $friend))
            ->assertRedirect();

        $this->assertDatabaseHas('follows', [
            'follower_id' => $me->id,
            'following_id' => $friend->id,
        ]);

        $this->actingAs($me)
            ->get(route('friends.index'))
            ->assertSee('Подписки')
            ->assertSee('Михаил Петров');

        $this->actingAs($friend)
            ->get(route('friends.index'))
            ->assertSee('Подписчики')
            ->assertSee('Анна Иванова');
    }

    public function test_user_cannot_follow_themselves_and_a_repeat_does_not_duplicate(): void
    {
        $me = User::factory()->create();
        $friend = User::factory()->create();

        $this->actingAs($me)
            ->from(route('friends.index'))
            ->post(route('friends.store', $me))
            ->assertRedirect(route('friends.index'))
            ->assertSessionHasErrors('friend');

        $this->actingAs($me)->post(route('friends.store', $friend));
        $this->actingAs($me)->post(route('friends.store', $friend));

        $this->assertDatabaseCount('follows', 1);

        $this->actingAs($me)
            ->delete(route('friends.destroy', $friend))
            ->assertRedirect();

        $this->assertDatabaseCount('follows', 0);
    }
}
