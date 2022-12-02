<?php

namespace App\Repository\Auth\v1;

use App\Http\Requests\v1\AuthRequest;
use App\Models\User;
use App\Repository\Repositpry;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements Repositpry
{

    public function model()
    {
        return User::query();
    }

    public function store(AuthRequest $request)
    {
        return $this->model()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password)
        ]);
    }
}
