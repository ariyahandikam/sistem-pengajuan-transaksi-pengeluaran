<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ];
    }
}
