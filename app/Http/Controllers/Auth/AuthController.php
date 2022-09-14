<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    /**
     * @return View
     */
    public function showLogin(){
        return View('login.login_form');
    }

    /**
     * @param App\Http\Requests\LoginFormRequest $request
     */

    public function login(LoginFormRequest $request){
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect('home')->with('login_success', 'ログインに成功しました。');
        }

        return back()->withErrors([
            'login_error' => 'メールアドレスかパスワードが間違っています。',
        ]);
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('showLogin')->with('logout', 'ログアウトしました。');;
    }

}
