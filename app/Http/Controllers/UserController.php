<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->with('role')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::query()->create($request->validated());
        return back()->with('success', 'User created');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $payload = $request->validated();
        if (empty($payload['password'])) {
            unset($payload['password']);
        }

        $user->update($payload);
        return back()->with('success', 'User updated');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated');
    }
}
