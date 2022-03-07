<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminAuthToken;
use Error;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'admins' => Admin::all()
            ]
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
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|max:255'
            ]);

            $admin = new Admin;
            $admin->user_name = $request->email;
            $admin->password = password_hash($request->password, PASSWORD_DEFAULT);
            $admin->save();

            return response()->json([
                'created' => true,
                'data' => [
                    'admin' => $admin
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'created' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'admin' => $admin
            ]
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        try {
            $admin->email = is_null($request->email) ? $admin->email : $request->email;
            $admin->password = is_null($request->password) ? $admin->password : password_hash($request->password, PASSWORD_DEFAULT);
            $admin->save();


            return response()->json([
                'updated' => true,
                'data' => [
                    'admin' => $admin
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'updated' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        try {
            $admin->delete();
            return response()->json([
                'deleted' => true,
                'data' => [
                    'admin' => $admin
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'deleted' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


    public function login(Request $request)
    {
        try {
            $admin = Admin::where('email', $request->email)->first();
            if (!$admin) throw new Error("User Not Found");
            if (password_verify($request->password, $admin->password)) {
                $token = rand(1, 10000) . time();
                $adminAuthToken = new AdminAuthToken;
                // $adminAuthToken->admin_auth_token = $token;
                // $adminAuthToken->admin_id = $admin->id;
                // $adminAuthToken->save();
                $adminAuthToken->admin_auth_token = $token;
                $admin->admin_token()->save($adminAuthToken);

                return response()->json([
                    'status' => true,
                    'data' => [
                        'admin' => $admin->email,
                        'adminAuthToken' => $token
                    ]
                ]);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'message' => "Fail login."
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


    public function logout(Request $request)
    {
        try {
            $adminAuthToken = AdminAuthToken::where('admin_auth_token', $request->header('admin-token'))->first();
            $adminAuthToken->delete();
            return response()->json([
                'status' => true,
                'logout' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'logout' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
