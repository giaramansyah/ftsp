<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\SecureHelper;
use App\Library\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LandingController extends Controller
{

    public function index()
    {   
        return view('contents.landing.index');
    }

    public function auth(Request $request)
    {
        $param = SecureHelper::unpack($request->input('json'));

        if (!is_array($param)) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $user = User::where('username', $param['username'])->where('is_trash', 0)->first();
        if ($user !== null) {
            $password = $user->username . $param['password'] . $user->hash;
            $remember = isset($param['remember']) ? true : false;
            $is_verified = Hash::check($password, $user->password);
            if($is_verified) {
                if($user->is_new) {
                    $response = new Response(true, __('Welcome'), 1);
                    $response->setRedirect(route('password.guest', ['action' => config('global.action.password.add'), 'id' => SecureHelper::secure($user->id)]));
                } else if($user->is_login) {
                    $response = new Response(false, __('Your account currently logged in'));
                } else if ($user->is_remember) {
                    $response = new Response(false, __('Your account is linked to another browser/device'));
                } else {
                    if (Auth::attempt(['username' => $param['username'], 'password' => $password], $remember)) {
                        // if ($remember) {
                        //     $user->is_remember = 1;
                        //     $user->save();
                        // }
                        $response = new Response(true, __('Welcome'), 1);
                        $response->setRedirect(route('home'));

                        $this->writeAppLog('AUTH');
                    } else {
                        $response = new Response(false, __('Invalid credentials'));
                    }
                }
            } else {
                $response = new Response(false, __('Invalid credentials'));
            }
        } else {
            $response = new Response(false, __('Account not registered'));
        }

        return response()->json($response->responseJson());
    }

    public function logout()
    {
        $user = User::where('username', Auth::user()->username)->first();
        $user->is_login = 0;
        $user->is_remember = 0;
        $user->remember_token = null;
        $user->save();

        $this->writeAppLog('LOUT');

        Session::flush();
        Auth::logout();

        return redirect(route('landing'));
    }
}
