<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string',
                'email' => 'required|string',
                // password validator use this https://stackoverflow.com/questions/31539727/laravel-password-validation-rule
                'password' => 'required|string'
            ]
        );

        if (User::where('email', $request->email)->first()) {
            return [
                "success" => false,
                "message" => "Email already exists"
            ];
        } else {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return [
                "success" => true,
                "user" => $user
            ];
        }
    }



    public function login(Request $request)
    {
        $token = "";
        $role = "";

        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                "success" => false,
                "message" => "credentail didn't match"
            ];
        }

        // token creation 
        if ($user->email === "aa@gmail.com") { // admin token
            $token  = $user->createToken('adminToken', ['role:addproduct'])->plainTextToken;
            $role = "admin";
        } else { // user token
            $token = $user->createToken('userToken')->plainTextToken;
            $role = "user";
        }

        /*  checking roles
        if ($user->tokenCan('server:update')) {
        
        } */

        return [
            "success" => true,
            "name" => $user->name,
            "token" => $token,
            "role" => $role
        ];
    }

    //logout receives no data from post method but we can still access user as token is sent here
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return [
            "success" => true,
        ];
    }
}
