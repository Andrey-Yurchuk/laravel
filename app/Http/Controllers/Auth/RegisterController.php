<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\UserServiceInterface;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(
        private UserServiceInterface $userService
    ) {
    }

    public function showRegistrationForm(): View
    {
        return view('pages.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = $this->userService->register($request->validated());

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->role === UserRole::Admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->role === UserRole::Instructor) {
            return redirect()->intended(route('instructor.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }
}
