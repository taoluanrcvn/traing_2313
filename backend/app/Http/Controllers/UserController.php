<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
            $users = User::paginate($perPage);
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
        return response()->json([
            "statusCode" => true
        ]);
    }

    public function blockUser(Request $request) {
        $idUserBlock = (int) $request->idUserBlock;
        $user = User::where('id', $idUserBlock);
        if (!$user) {

        }
        $user->is_active = !$user->is_active;
        return response()->json([
            "statusCode" => true,
            "data" => $idUserBlock
        ]);
    }

    public function test(Request $request) {
        return response()->json([
            "statusCode" => $request->all(),
            "data" => auth::user(),
            "hi" => auth::parseToken()->authenticate()
        ]);
    }
}
