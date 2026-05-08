<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{

    public function login()
    {
        if (url()->previous() && !str_contains(url()->previous(), '/login')) {
            session(['url.intended' => url()->previous()]);
        }

        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }
}
