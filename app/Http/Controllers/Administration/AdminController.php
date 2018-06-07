<?php

namespace App\Http\Controllers\Administration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api');
        $this->middleware(['role:' . config('role.names.super_admin')]);
    }

    /**
     * Sends all outstanding and need to see information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUsers(Request $request)
    {
        return \App\Models\User::latest('id')->get();
    }

    /**
     * Sends all outstanding and need to see information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUser(\App\Models\User $user)
    {
        return $user;
    }
}
