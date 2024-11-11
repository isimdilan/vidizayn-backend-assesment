<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $validationRules = [
            'name'=>'required|string|max:250',
            'email'=>'required|string|email|max:250|unique:users',
            'password'=>'required',
            'role'=>'required|string|in:customer,provider',
        ];

        if($request->role === 'provider'){
            $validationRules['specialty'] = 'required|string';
            $validationRules['details'] = 'required|string';
            $validationRules['working_hours'] = 'required|string';
        }

        $request->validate($validationRules);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>$request->role,
        ]);

        if ($request->role === 'provider'){
            Provider::create([
                'user_id'=>$user->id,
                'specialty'=>$request->specialty,
                'details'=>$request->details,
                'working_hours'=>$request->working_hours
            ]);
        }
        return response()->json(['message' => 'Registration successful']);
    }

    public function login(Request $request)
    {
        if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json(['message' => 'Login failed'], 401);
        }

        $user = Auth::user();

        if($user->role === 'provider'){
            return response()->json(['message'=>'Provider Login']);
        } else {
            return response()->json(['message'=>'Customer Login']);
        }
    }
}
