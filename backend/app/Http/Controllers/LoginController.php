<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request) {
        $credentials = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5',
        ], [
            'email.required'    => 'E-mail không được để trống!',
            'email.email'       => 'E-mail không đúng định dạng!',
            'password.required' => 'E-mail không được để trống!',
            'password.min' => 'Mật khẩu phải hơn 5 ký tự!',
        ]);

        if ($credentials->fails()) {
            return response()->json([
                'statusCode' => false,
                'messages' => $credentials->errors(),
            ]);
        }

        $data = $credentials->validated();

        $email = $data['email'];
        $password = $data['password'];
        $remember = (boolean) $request->remember;

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'statusCode' => false,
                'messages' => [ 'email' => 'E-mail không tồn tại!'],
            ]);
        }

        $user->makeVisible('password')->toArray();
        if ($user->password !== md5($password)) {
            return response()->json([
                'statusCode' => false,
                'messages' => [ 'password' => 'Mật khẩu không đúng!'],
            ]);
        }

        if (! $token = auth('api')->login( $user )) {
            return response()->json(['messages' => 'Unauthorized'], 401);
        }
        $user->last_login_at = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');;
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
