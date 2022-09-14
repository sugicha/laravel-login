<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;



class AuthController extends Controller
{
    /**
     * @return View
     */
    public function showLogin(){
        return View('login.login_form');
    }

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param App\Http\Requests\LoginFormRequest $request
     */

    public function login(LoginFormRequest $request)
    {
        $credentials = $request->only('email', 'password');

        //$user = User::where('email', '=', $credentials['email'])->first();
        $user = $this->user->getUserByEmail($credentials['email']);

        if (!is_null($user)){

            //if ($user->locked_flg === 1){
            if ($this->user->isAccountLocked($user)){
                return back()->withErrors([
                    'login_error' => 'アカウントがロックされています。',
                ]);
            }
        

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                //if ($user->error_count > 0){
                //    $user->error_count = 0;
                //    $user->save();
                //}
                $this->user->resetErrorCount($user);

                return redirect('home')->with('login_success', 'ログインに成功しました。');
             }

             //$user->error_count = ($user->error_count + 1);
             $user->error_count = $this->user->addErrorCount($user->error_count);

             //if ($user->error_count > 5){
            //    $user->locked_flg = 1;

//                $user->save();

  //              return back()->withErrors([
    //                'login_error' => 'アカウントがロックされました。',
      //          ]);
        //    }

            if ($this->user->lockAccount($user)){
                return back()->withErrors([
                    'login_error' => 'アカウントがロックされました。',
                ]);
            }

            $user->save();    
        }


        return back()->withErrors([
            'login_error' => 'メールアドレスかパスワードが間違っています。',
        ]);
    

        /**
         * @param  \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        //public function logout(Request $request)
        //{
        //    Auth::logout();
        //    $request->session()->invalidate();
        //    $request->session()->regenerateToken();
        //    return redirect()->route('showLogin')->with('logout', 'ログアウトしました。');
        //}
    }
}

