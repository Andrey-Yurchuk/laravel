<?php

namespace App\Livewire;

use App\Contracts\Services\UserServiceInterface;
use App\Enums\UserRole;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
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
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:student,instructor',
        'terms' => 'accepted',
    ];

    protected array $messages = [
        'name.required' => 'Имя обязательно для заполнения',
        'name.min' => 'Имя должно содержать минимум 3 символа',
        'email.required' => 'Email обязателен для заполнения',
        'email.email' => 'Введите корректный email',
        'email.unique' => 'Пользователь с таким email уже существует',
        'password.required' => 'Пароль обязателен для заполнения',
        'password.min' => 'Пароль должен содержать минимум 8 символов',
        'password.confirmed' => 'Пароли не совпадают',
        'role.required' => 'Выберите роль',
        'role.in' => 'Недопустимая роль',
        'terms.accepted' => 'Необходимо согласиться с условиями использования',
    ];

    public function updated(string $field): void
    {
        if ($this->submitted) {
            $this->validateOnly($field);
        }
    }

    public function submit(UserServiceInterface $userService): void
    {
        $this->submitted = true;
        $this->validate();

        $user = $userService->register([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ]);

        Auth::login($user);
        request()->session()->regenerate();

        if ($user->role === UserRole::Admin) {
            $this->redirect(route('admin.dashboard'));
            return;
        }

        if ($user->role === UserRole::Instructor) {
            $this->redirect(route('instructor.dashboard'));
            return;
        }

        $this->redirect(route('dashboard'));
    }

    public function render(): View
    {
        return view('livewire.register-form');
    }
}
