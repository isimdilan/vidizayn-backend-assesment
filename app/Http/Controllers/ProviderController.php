<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProviderController extends Controller
{
    //
    public function index()
    {
        $providers = Provider::with('user')->get();
        return response()->json($providers);
    }

    public function show(Provider $provider)
    {
        return response()->json($provider->load('user'));
    }
}
