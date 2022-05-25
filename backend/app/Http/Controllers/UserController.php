<?php

namespace App\Http\Controllers;

use App\Http\Response\ResponseJson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Exception;
use Tymon\JWTAuth\Facades\JWTAuth as auth;

class UserController extends Controller
{
    /**
     * Lấy danh sách người dùng (tìm kiếm, phần trang).
     *
     * @return \Illuminate\Http\JsonResponse
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
        return ResponseJson::success($users);
    }

    /**
     * Thêm người dùng mới
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userCurrent = auth::user();
        if ($userCurrent->group_role !== 'Admin') {
            return ResponseJson::error([ "role" => trans('messages.role.not_permission')]);
        }
        $credentials = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5',
            'password_confirm' => 'required|min:5',
            'group_role' => 'required',
            'is_active' => 'required|integer',
            'name' => 'required|min:5'
        ], [
            'email.email' => trans('messages.email.malformed'),
            'password.min' => trans('messages.password.min'),
            'is_active.required' => trans('messages.is_active.required'),
            'group_role.required' => trans('messages.group_role.required'),
            'name.required' => trans('messages.user.required'),
            'name.min' => trans('messages.user.min_length_name'),
            'password_confirm.required' => trans('messages.password_confirm.required'),
        ]);

        if ($credentials->fails()) {
            return ResponseJson::error($credentials->errors());
        }
        $userNew = $credentials->validated();

        if ($userNew['group_role'] === 'Admin' && $userCurrent->id !== 1) {
            return ResponseJson::error([ 'group_role' => trans('messages.group_role.not_permission')]);
        }

        $user = User::where('email', $userNew['email'])->first();
        if ($user) {
            return ResponseJson::error(['email' => trans('messages.email.exist')]);
        }

        if ($userNew["password"] !== $userNew["password_confirm"]) {
            return ResponseJson::error(['email' => trans('messages.password_confirm.mismatched')]);
        }

        $user = new User;
        $user->email = $userNew["email"];
        $user->password = md5($userNew["password"]);
        $user->group_role = $userNew["group_role"];
        $user->is_active = $userNew["is_active"];
        $user->name = $userNew["name"];
        $user->save();

        return ResponseJson::success($user);
    }

    /** Khóa và mở khóa cho user */
    public function lockOrUnlockUser(Request $request) {
        $idUserBlock = (int) $request->idUserBlock;
        $type = $request->type;
        $userCurrent = auth::user();
        $user = User::where("id", $idUserBlock)->first();
        if (!$user) {
            return ResponseJson::error([ 'detail' => trans('messages.user.not_exist')]);
        }
        if ($user->group_role === "Admin") {
            return ResponseJson::error([ 'detail' => $type === 'lock' ? trans('messages.user.not_permission_lock') : trans('messages.user.not_permission_unlock')]);
        }
        if ($userCurrent->group_role !== 'Admin') {
            return ResponseJson::error([ 'detail' => trans('messages.user.not_permission')]);
        }

        $user->is_active = $type === 'lock' ? 0 : 1;
        $user->save();
        return ResponseJson::success();
    }

    /**
     * Xóa user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->group_role === "Admin") {
            return ResponseJson::error([ 'detail' => trans('messages.user.not_permission_delete')]);
        }
        $userCurrent = auth::user();
        if ($userCurrent->group_role !== 'Admin') {
            return ResponseJson::error([ 'detail' => trans('messages.user.not_permission')]);
        }

        $user->is_delete = 1;
        $user->save();
        return ResponseJson::success($user);
    }

    /**
     * Cập nhật thông tin người dùng
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $credentials = Validator::make($request->all(), [
            'email' => 'required|email',
            'group_role' => 'required',
            'is_active' => 'required|integer',
            'name' => 'required|min:5',
            'password' => 'sometimes|min:5',
        ], [
            'email.required' => trans('messages.email.required'),
            'is_active.required' => trans('messages.is_active.required'),
            'group_role.required' => trans('messages.group_role.required'),
            'name.required' => trans('messages.user.required'),
            'name.min' => trans('messages.user.min_length_name'),
            'password.min' => trans('messages.password.min'),
        ]);

        if ($credentials->fails()) {
            return ResponseJson::error($credentials->errors());
        }

        $userNew = $credentials->validated();

        if ($userNew['email'] !== $user["email"]) {
            $existEmail = User::where('email', $userNew['email'])->first();
            if ($existEmail) {
                return ResponseJson::error(["email" => trans('messages.email.exist')]);
            }
        }

        if (isset($userNew["password"])) {
            $userNew["password"] = md5($userNew["password"]);
        }

        User::where('id', $user["id"])->update($userNew);

        return ResponseJson::success();
    }

    /**
     * Trả về thông tin user khi xác thực.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $userCurrent = auth::user();
        return ResponseJson::success($userCurrent);
    }
}
