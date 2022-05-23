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
            'email.required'    => 'E-mail không được để trống!',
            'email.email'       => 'E-mail không đúng định dạng!',
            'password.required' => 'Mật khẩu không được để trống!',
            'password.min' => 'Mật khẩu phải hơn 5 ký tự!',
        ]);

        if ($credentials->fails()) {
            return response()->json([
                'statusCode' => false,
                'messages' => $credentials->errors(),
            ], Response::HTTP_OK);
        }

        $data = $credentials->validated();

        $email = $data['email'];
        $password = $data['password'];
        $user = User::where('email', $email)->first();
        if (!$user) {
           return ResponseJson::error([ 'email' => trans('messages.email_not_exist')]);
        }

        $user->makeVisible('password')->toArray();
        if ($user->password !== md5($password)) {
            return ResponseJson::error([ 'password' => trans('messages.password_fail')]);
        }

        if ($user->is_delete == 1) {
            return ResponseJson::error([ 'other' => trans('messages.user_delete')]);
        }

        if ($user->is_active != 1) {
            return ResponseJson::error([ 'other' => trans('messages.user_lock')]);
        }

        if (! $token = auth('api')->login( $user )) {
            return ResponseJson::error([ 'other' => trans('messages.unauthorized')], Response::HTTP_UNAUTHORIZED);
        }

        $user->last_login_at = Carbon::now()->format('Y-m-d H:i:s');;
        $user->last_login_ip = $request->ip();
        $user->save();
        return $this->createNewToken($token);

    }

    protected function createNewToken($token){
        $user = auth('api')->user();
        unset($user['password']);
        return response()->json([
            'statusCode' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user
        ]);
    }
}
