<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Method Not Allowed'
        ],405);
    }

    public function create(Request $request)
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:6',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['message'=>'Bad Request','errors' => $validator->errors()],400); 
        }
        $user_type = 1;
        if($request->type == 0){
            $user_type = 0;
        }
        DB::beginTransaction();
        try{
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'type'     => $user_type
            ]);
            DB::commit();
            $token = $user->createToken('Token')->accessToken;
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'type'   => $user->type,
                    'access_token'  => $token
                ],
            ],201);
        }
        catch(\Exception $err){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'data' => null
            ],500);
        }
    }

    public function login(Request $request){
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message'=>'Bad Request','errors' => $validator->errors()],400); 
        }
        if(auth()->attempt($request->all())) {
            $user = Auth::user();
            $token = $user->createToken('Token')->accessToken;
            return response()->json(['message'=>'Login successfully',
                'data' => [
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'type'   => $user->type,
                    'access_token'  => $token
                ]
            ], 200);
        }
        return response()->json([
            'message' => 'Invalid credentials'
        ], 402);

    }
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ],200);
    }
}
