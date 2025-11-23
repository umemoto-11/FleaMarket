<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\ValidationException;

class CustomLoginController extends Controller
{
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        return redirect()->intended('/');
    }
}
