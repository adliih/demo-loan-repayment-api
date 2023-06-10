<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthController extends Controller
{
  /**
   * Register a new user.
   *
   * @param  \App\Http\Requests\RegisterRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function register(RegisterRequest $request)
  {
    /** @var User */
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password),
      'role' => $request->role,
    ]);

    $accessToken = $user->createToken('default')->plainTextToken;

    return response()->json(['message' => 'Registration successful', 'user' => $user, 'access_token' => $accessToken], 201);
  }

  /**
   * Login user and create token.
   *
   * @param  \App\Http\Requests\LoginRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(LoginRequest $request)
  {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
      /** @var User */
      $user = Auth::user();
      $accessToken = $user->createToken('default')->plainTextToken;

      return response()->json(['user' => $user, 'access_token' => $accessToken], 200);
    }

    throw new BadRequestHttpException("Invalid credentials");
  }
}