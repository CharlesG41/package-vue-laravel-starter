<?php

namespace Cyvian\Src\App\Http\Controllers;

use Cyvian\Src\App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->input('email'))->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            Session::put('auth.user', $user);
            Session::put('auth.id', $user->id);
            return Redirect::route('manager.create', ['entryType' => 'news']);
        } else {
            return Inertia::render(
                'Login',
                [
                    'errors' => [
                        'credentials' => __('cyvian.errors.credentials')
                    ]
                ]
            );
        }
    }

    public function logout(Request $request)
    {
        Session::put('auth', null);
        return Redirect::route('login');
    }
}
