<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FriendController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->input('q', ''));
        $people = collect();
        if ($query !== '') {
            $like = '%'.addcslashes($query, '%_\\').'%';
            $people = User::query()
                ->where(function ($builder) use ($like) {
                    $builder->where('name', 'like', $like)
                        ->orWhere('username', 'like', $like);
                })
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        $following = $request->user()->following()->orderBy('name')->get();

        return view('friends.index', [
            'query' => $query,
            'people' => $people,
            'following' => $following,
            'followers' => $request->user()->followers()->orderBy('name')->get(),
            'followingIds' => $following->pluck('id'),
        ]);
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        if ((int) $user->id === (int) $request->user()->id) {
            throw ValidationException::withMessages([
                'friend' => 'На себя подписываться не нужно.',
            ]);
        }

        $request->user()->following()->syncWithoutDetaching([$user->id]);

        return back()->with('status', 'Вы подписались.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $request->user()->following()->detach($user->id);

        return back()->with('status', 'Подписка снята.');
    }
}
