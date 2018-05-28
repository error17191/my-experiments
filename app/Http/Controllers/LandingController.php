<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index(Request $request){
        $agency = $request->route('agency');
        if($agency == 'main'){
            return view('auth.login');
        }
    }
}
