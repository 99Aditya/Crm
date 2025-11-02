<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;


class AuthController extends Controller
{
    public function login(){
        return view('login');
    }


    public function Auth(Request $request){

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (auth()->attempt($credentials)) {
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
        
    }

    public function dashboard(Request $request){
        $user = Auth::user();   
        $all_contact = Contact::where('is_active',1)->count();
      
        return view('dashboard',compact('user','all_contact'));
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }
}
