<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function fetchAllUser()
    {
        try {
            $users = User::get();
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function getUser(Request $request)
    {
        // dd($request);
        try {
            $user = User::find($request->id);

            if ($user) {
                return response()->json([
                    'success' => true,
                    'data' => $user
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'User not found'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $existUser = User::where('email', $request->email)->first();
            if ($existUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User Already Registered!'
                ], 400);
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = \bcrypt($request->password);
            $user->save();

            $users['name'] = $user->name;
            $users['email'] = $user->email;

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found!'
                ], 400);
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = \bcrypt($request->password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Update User Success'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found!'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Delete User Success'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
