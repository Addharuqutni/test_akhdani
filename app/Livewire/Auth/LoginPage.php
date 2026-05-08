<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoginPage extends Component
{
    public string $username = '';
    public string $password = '';

    protected array $rules = [
        'username' => ['required', 'string'],
        'password' => ['required', 'string'],
    ];

    public function login(): void
    {
        $credentials = $this->validate();

        if (!Auth::attempt($credentials)) {
            $this->addError('username', 'Username/password tidak valid');
            return;
        }

        request()->session()->regenerate();
        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login-page')->layout('layouts.app', ['title' => 'Login']);
    }
}
