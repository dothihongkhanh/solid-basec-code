<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\User\CreateUserService;
use App\Services\User\DeleteUserService;
use App\Services\User\GetUserService;
use App\Services\User\UpdateUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $user = resolve(CreateUserService::class)->setParams($request)->handle();
        return redirect()->route('users.index');
    }

    public function delete(int $id, Request $request)
    {
        $user = resolve(DeleteUserService::class)->handle();
        return redirect()->route('users.index');
    }

    public function index(Request $request)
    {
        $user = resolve(GetUserService::class)->handle();
        return redirect()->route('users.index');
    }

    public function update(Request $request)
    {
        $user = resolve(UpdateUserService::class)->setParams($request)->handle();
        return redirect()->route('users.index');
    }

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        $createUserService = resolve(CreateUserService::class);
        $user = $createUserService->setParams($validatedData)->handle();

        if ($user) {
            return response()->json(['user' => $user], 201);
        } else {
            return response()->json(['error' => 'Registration failed'], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            return response()->json(['user' => $user, 'message' => 'Login success'], 201);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
