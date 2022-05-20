<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Exception;
use Tymon\JWTAuth\Facades\JWTAuth as auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage;
        $isSearch = $request->isSearch;
        if (!$isSearch) {
            $users = User::where('is_delete' , 0)->paginate($perPage);
        } else {
            $searchName = $request->name;
            $searchEmail = $request->email;
            $searchGroup = $request->group;
            $searchStatus = $request->status ;

            $users = User::where(function ($query) use ($searchGroup) {
                    if ($searchGroup) {
                        $query->where('group_role', $searchGroup);
                    }
                })->where(function ($query) use ($searchName, $searchEmail) {
                    if ($searchName) {
                        $query->where('name', 'like', '%'.$searchName.'%');
                    }
                    if ($searchEmail) {
                        $query->orWhere('email', 'like', '%'.$searchEmail.'%');
                    }
                })->where(function ($query) use ($searchStatus) {
                    if (isset($searchStatus)) {
                        $query->where('is_active', $searchStatus);
                    }
                })->where('is_delete' , 0)->paginate($perPage);;
        }
        return response()->json([
            "statusCode" => true,
            "data" => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userCurrent = auth::user();
        if ($userCurrent->group_role !== 'Admin') {
            return response()->json([
                "statusCode" => false,
                "messages" => [ "role" => "Bạn không có quyền thực hiện tính năng này"]
            ]);
        }
        $credentials = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5',
            'password_confirm' => 'required|min:5',
            'group_role' => 'required',
            'is_active' => 'required|integer',
            'name' => 'required|min:5'
        ], [
            'email.email'       => 'E-mail không đúng định dạng',
            'password.min' => 'Mật khẩu phải hơn 5 ký tự',
            'is_active.required' => 'Chưa chọn trạng thái',
            'group_role.required' => 'Chưa chọn nhóm',
            'name.required' => 'Chưa nhập tên',
            'name.min' => 'Tên phải dài hơn 5 ký tự',
            'password_confirm.required' => 'Chưa nhập mật khẩu xác nhận',
        ]);

        if ($credentials->fails()) {
            return response()->json([
                'status_code' => false,
                'messages' => $credentials->errors(),
            ]);
        }
        $userNew = $credentials->validated();

        if ($userNew['group_role'] === 'Admin' && $userCurrent->id !== 1) {
            return response()->json([
                "statusCode" => false,
                "messages" => [ "role" => "Bạn không có quyền thực hiện tính năng này"]
            ]);
        }

        $user = User::where('email', $userNew['email'])->first();
        if ($user) {
            return response()->json([
                "statusCode" => false,
                "messages" => ["email" => "E-mail đã tồn tại"]
            ]);
        }

        if ($userNew["password"] !== $userNew["password_confirm"]) {
            return response()->json([
                "statusCode" => false,
                "messages" => ["password" => "Mật khẩu không khớp"]
            ]);
        }

        $user = new User;
        $user->email = $userNew["email"];
        $user->password = md5($userNew["password"]);
        $user->group_role = $userNew["group_role"];
        $user->is_active = $userNew["is_active"];
        $user->name = $userNew["name"];
        $user->save();

        return response()->json([
            "statusCode" => true,
            "data" => $user
        ]);
    }

    public function lockOrUnlockUser(Request $request) {

        $idUserBlock = (int) $request->idUserBlock;
        $type = $request->type;
        $userCurrent = auth::user();
        $user = User::where("id", $idUserBlock)->first();
        if (!$user) {
            return response()->json([
                "statusCode" => false,
                "messages" => "Người dùng không tồn tại!"
            ]);
        }
        if ($user->group_role === "admin") {
            return response()->json([
                "statusCode" => false,
                "messages" => "Người dùng này không được " . $type === 'lock' ? "khóa" : "mở khóa" . "!"
                ]);
        }
        if ($userCurrent->group_role !== 'Admin') {
            return response()->json([
                "statusCode" => false,
                "messages" => "Bạn không có quyên để thực hiện tính năng này!"
            ]);
        }

        $user->is_active = $type === 'lock' ? 0 : 1;
        $user->save();
        return response()->json([
            "statusCode" => true,
        ]);
    }

    public function test(Request $request) {
        return response()->json([
            "statusCode" => $request->all(),
            "data" => auth::user(),
            "hi" => auth::parseToken()->authenticate()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->group_role === "admin") {
            return response()->json([
                "statusCode" => false,
                "messages" => "Người dùng này không được xóa!"
            ]);
        }
        $userCurrent = auth::user();
        if ($userCurrent->group_role !== 'Admin') {
            return response()->json([
                "statusCode" => false,
                "messages" => "Bạn không có quyên để thực hiện tính năng này!"
            ]);
        }

        $user->is_delete = 1;
        $user->save();
        return response()->json([
            "statusCode" => true,
            $user
        ]);
    }
}
