<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class AdminLoginController extends Controller
{
    public function login() {
        return view('admin.login');
    }

    public function post_login(Request $request) {
       // dd($request);
        $credentials = $request->only('email', 'password');

        if(Auth::attempt($credentials)) {
           // dd(Auth::user()->type);
            if(Auth::user()->type === '0') {
            Session::put('admin_id', Auth::id());
            return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('admin.logout');

            }
        } else {
            return redirect()->back()->withErrors(['credential' => 'invalid credentials']);
        }
    }

    public function dashboard() {
        // dd($request);
        return view('admin.dashboard');
     }

     public function logout() {
        Auth::logout();
        return redirect()->route('admin.login');
     }


}
