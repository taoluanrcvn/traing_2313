<?php

namespace App\Http\Controllers;

use App\Http\Response\ResponseJson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    /** Người dùng đăng nhập  */
    public function authenticate(Request $request) {
        $credentials = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5',
        ], [
            'email.required'    => trans('messages.email.required'),
            'email.email'       => trans('messages.email.malformed'),
            'password.required' => trans('messages.password.required'),
            'password.min' => trans('messages.password.min'),
        ]);

        if ($credentials->fails()) {
            return ResponseJson::error($credentials->errors());
        }

        $data = $credentials->validated();

        $email = $data['email'];
        $password = $data['password'];
        $user = User::where('email', $email)->first();
        if (!$user) {
           return ResponseJson::error(['email' => trans('messages.email.not_exist')]);
        }

        $user->makeVisible('password')->toArray();
        if ($user->password !== md5($password)) {
            return ResponseJson::error(['password' => trans('messages.password.fail')]);
        }

        if ($user->is_delete == 1) {
            return ResponseJson::error(['other' => trans('messages.user.deleted')]);
        }

        if ($user->is_active != 1) {
            return ResponseJson::error(['other' => trans('messages.user.locked')]);
        }

        if (! $token = auth('api')->login( $user )) {
            return ResponseJson::error(['other' => trans('messages.auth.unauthorized')], Response::HTTP_UNAUTHORIZED);
        }

        $user->last_login_at = Carbon::now()->format('Y-m-d H:i:s');;
        $user->last_login_ip = $request->ip();
        $user->save();

        $user = auth('api')->user();
        unset($user['password']);
        return ResponseJson::success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user
        ]);
    }
}
