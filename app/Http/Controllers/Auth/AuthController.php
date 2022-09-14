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

        $user = User::where('email', '=', $credentials['email'])->first();
        if (!is_null($user)){

            if ($user->locked_flg === 1){
                return back()->withErrors([
                    'login_error' => 'アカウントがロックされています。',
                ]);
            }

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                if ($user->error_count > 0){
                    $user->error_count = 0;
                    $user->save();
                }

                return redirect('home')->with('login_success', 'ログインに成功しました。');
             }

             $user->error_count = ($user->error_count + 1);
             if ($user->error_count > 5){
                $user->locked_flg = 1;

                $user->save();

                return back()->withErrors([
                    'login_error' => 'アカウントがロックされました。',
                ]);
            }

            $user->save();
            
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
