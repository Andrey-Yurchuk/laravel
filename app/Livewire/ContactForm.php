<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $message = '';
    public bool $submitted = false;

    protected array $rules = [
        'name' => 'required|min:2',
        'email' => 'required|email',
        'message' => 'required|min:10',
    ];

    protected array $messages = [
        'name.required' => 'Имя обязательно для заполнения',
        'name.min' => 'Имя должно содержать минимум 2 символа',
        'email.required' => 'Email обязателен для заполнения',
        'email.email' => 'Введите корректный email',
        'message.required' => 'Сообщение обязательно для заполнения',
        'message.min' => 'Сообщение должно содержать минимум 10 символов',
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

        Session::flash('contact_message', 'Спасибо за ваше сообщение! Мы свяжемся с вами в ближайшее время.');

        $this->reset(['name', 'email', 'message', 'submitted']);
    }

    public function render(): View
    {
        return view('livewire.contact-form');
    }
}
