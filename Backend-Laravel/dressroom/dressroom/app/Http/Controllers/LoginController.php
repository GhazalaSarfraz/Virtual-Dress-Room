<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;

class LoginController extends Controller
{
    public function index()
    {
        $setting = SiteSetting::first();

        return view('auth.login', compact('setting'));
    }
}