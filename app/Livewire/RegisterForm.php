<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class RegisterForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'student';
    public bool $terms = false;
    public bool $submitted = false;

    protected array $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
        'role' => 'required|in:student,instructor',
        'terms' => 'accepted',
    ];

    protected array $messages = [
        'name.required' => 'Имя обязательно для заполнения',
        'name.min' => 'Имя должно содержать минимум 3 символа',
        'email.required' => 'Email обязателен для заполнения',
        'email.email' => 'Введите корректный email',
        'password.required' => 'Пароль обязателен для заполнения',
        'password.min' => 'Пароль должен содержать минимум 8 символов',
        'password.confirmed' => 'Пароли не совпадают',
        'terms.accepted' => 'Необходимо согласиться с условиями использования',
    ];

    public function updated(string $field): void
    {
        if ($this->submitted) {
            $this->validateOnly($field);
        }
    }

    public function submit(): void
    {
        $this->submitted = true;
        $this->validate();

        Session::flash('message', 'Регистрация прошла успешно!');

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'terms', 'submitted']);
    }

    public function render(): View
    {
        return view('livewire.register-form');
    }
}
