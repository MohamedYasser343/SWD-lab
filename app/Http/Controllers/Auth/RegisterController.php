<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $validated['username'] = $this->uniqueUsernameFor($validated['name']);
        $validated['role'] = UserRole::Reader->value;

        $user = User::create($validated);

        Auth::login($user);

        return redirect()
            ->route('posts.index')
            ->with('status', 'Welcome to the lagoon, ' . $user->name . '.');
    }

    private function uniqueUsernameFor(string $name): string
    {
        $base = Str::slug($name) ?: 'reader';
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . '-' . $counter++;
        }

        return $username;
    }
}
