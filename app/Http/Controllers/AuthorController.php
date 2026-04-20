<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class AuthorController extends Controller
{
    public function show(User $user): View
    {
        $posts = $user->posts()
            ->with(['category', 'tags'])
            ->published()
            ->latest('published_at')
            ->paginate(6);

        return view('authors.show', [
            'author' => $user,
            'posts' => $posts,
        ]);
    }
}
