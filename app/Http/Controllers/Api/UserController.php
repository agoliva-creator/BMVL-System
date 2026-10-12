<?php
namespace App\Http\Controllers\Api;

use App\Models\User;

class UserController extends CrudController
{
    protected string $model = User::class;

    protected function rules(bool $updating = false): array
    {
        $r = $this->required($updating);
        return ['name' => "$r|string|max:255", 'email' => "$r|email|max:255|unique:users,email" . ($updating ? ',' . request()->route('user') : ''), 'password' => $updating ? 'sometimes|string|min:8' : 'required|string|min:8', 'role' => 'sometimes|in:admin,staff,athlete', 'phone_number' => 'sometimes|nullable|string|max:30', 'phone' => 'sometimes|nullable|string|max:30', 'avatar' => 'sometimes|nullable|string|max:2048', 'gender' => 'sometimes|nullable|string|max:50', 'date_of_birth' => 'sometimes|nullable|date', 'address' => 'sometimes|nullable|string'];
    }
}
