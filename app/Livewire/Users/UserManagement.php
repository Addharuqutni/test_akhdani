<?php

namespace App\Livewire\Users;

use App\Livewire\Concerns\WithListDefaults;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithListDefaults, WithPagination;

    public string $search = '';
    public ?int $editing_user_id = null;
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public ?int $role_id = null;
    public string $password = '';
    public bool $is_active = true;

    public array $roles = [];

    public function mount(): void
    {
        $this->roles = Role::query()->orderBy('name')->get(['id', 'name', 'code'])->toArray();
    }

    protected function rules(): array
    {
        $userId = $this->editing_user_id ?? 'NULL';

        return [
            'name' => ['required', 'string', 'min:3'],
            'username' => ['required', 'string', 'min:3', "unique:users,username,{$userId}"],
            'email' => ['required', 'email', "unique:users,email,{$userId}"],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => [$this->editing_user_id ? 'nullable' : 'required', 'string', 'min:8'],
        ];
    }

    public function save(): void
    {
        $payload = $this->validate();

        if ($this->editing_user_id) {
            $user = User::query()->findOrFail($this->editing_user_id);
            if (empty($payload['password'])) {
                unset($payload['password']);
            }
            $user->update($payload + ['is_active' => $this->is_active]);
            session()->flash('success', 'User berhasil diupdate.');
        } else {
            User::query()->create($payload + ['is_active' => $this->is_active]);
            session()->flash('success', 'User berhasil dibuat.');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $user = User::query()->with('role')->findOrFail($id);

        $this->editing_user_id = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role_id = $user->role_id;
        $this->is_active = $user->is_active;
        $this->password = '';
    }

    public function toggleActive(int $id): void
    {
        $user = User::query()->findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('success', 'Status user diperbarui.');
    }

    public function resetForm(): void
    {
        $this->reset(['editing_user_id', 'name', 'username', 'email', 'role_id', 'password']);
        $this->is_active = true;
    }

    public function render()
    {
        $rows = User::query()
            ->with('role')
            ->when($this->search, fn ($q) => $q
                ->where(function ($subQuery) {
                    $subQuery
                        ->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('username', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                }))
            ->latest()
            ->paginate($this->perPage)
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role?->code ?? '-',
                'is_active' => $user->is_active,
            ]);

        return view('livewire.users.user-management', compact('rows'))->layout('layouts.app', ['title' => 'User Management']);
    }
}

