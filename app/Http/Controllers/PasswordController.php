<?php

namespace App\Http\Controllers;

use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PasswordController extends Controller
{
    public function index($action, $id) 
    {
        if(!in_array($action, Arr::only(config('global.action.password'), ['add', 'forget']))) {
            return abort(404);
        }

        $plainId = SecureHelper::unsecure($id);
        if(!$plainId) {
            return abort(404);
        }

        $user = User::find($plainId)->where('is_new', 1)->where('is_trash', 0)->first();

        if(!$user) {
            return abort(404);
        }

        if($action === config('global.action.password.add')) {
            $view = ['action' => route('password.guest.post', ['action' => config('global.action.password.add'), 'id' => $id])];
            return view('contents.user.password.new', $view);
        }

        if($action === config('global.action.password.forget')) {
            $view = ['action' => route('password.guest.post', ['action' => config('global.action.password.forget'), 'id' => $id])];
            return view('contents.user.password.forget', $view);
        }
    }

    public function post(Request $request, $action, $id) 
    {
        if(!in_array($action, config('global.action.password'))) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $plainId = SecureHelper::unsecure($id);

        $param = SecureHelper::unpack($request->input('json'));

        if (!is_array($param) || !$plainId) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        if($action === config('global.action.password.add')) {
            $user = User::find($plainId);
            if($user) {
                $hash = md5(sha1(date('ymdHis')));
                $user->password = $user->username . $param['new_password'] . $hash;
                $user->hash = $hash;
                $user->is_new = 0;

                if($user->save()) {
                    $response = new Response(true, __('Password created successfuly, Please login to continue'), 1);
                    $response->setRedirect(route('landing'));

                    $this->writeAppLog('NPWD');
                } else {
                    $response = new Response(false, __('Create password failed. Please try again'));
                }
            } else {
                $response = new Response(false, __('Invalid credentials'));
            }
        }

        if($action === config('global.action.password.forget')) {
            $response = new Response();
        }

        if($action === config('global.action.password.edit')) {
            $response = new Response();
        }

        return response()->json($response->responseJson());
    }
}
