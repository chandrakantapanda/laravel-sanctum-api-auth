<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller {
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
            'c_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => 'Validation Error.',
                'data'    => $validator->errors(),
            ];
            return response()->json($response, 404);    
        }
        $fields = $request->all();
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'success' => true,
            'message' => 'User register successfully.',
            'data'    => $user,
        ];
        
        return response()->json($response, 200);
    }
    function login(Request $request){
        $user= User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            $response = [
                'success' => false,
                'message' => 'Unauthorised.',
            ];
            return response()->json($response, 404);
        }
    
        $result['token'] =  $user->createToken('my-app-token')->plainTextToken;
        $result['name']  =   $user->name;   
        $result['email']  =   $user->email;   
        $response = [
            'success' => true,
            'message' => 'User login successfully.',
            'data'    => $result,
        ];
        
        return response()->json($response, 200);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();
        $response = [
                'success' => true,
                'message' => 'User Logged Out.',
                'data'    => array(),
            ];        
        return response()->json($response, 200);
    }
}
