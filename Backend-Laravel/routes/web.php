<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/login', function () {
    return view('login');
});

Route::post('/login', function (Request $request) {

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {

        $user = Auth::user();

        // ADMIN
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        // USER
        return redirect('/user/dashboard');
    }

    return back()->withErrors([
        'email' => 'Invalid email or password',
    ]);
});

Route::get('/', function () {
    return view('welcome');
});