<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'course_id' => 'required|integer|exists:courses,id',
            'plan_id' => 'required|integer|exists:course_plans,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
        ];
    }
}
