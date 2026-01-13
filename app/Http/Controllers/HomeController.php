<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        return view('home');
    }
    public function about(Request $request)
    {
        return view('about');
    }
    public function terms(Request $request)
    {
        return view('terms');
    }
    public function policy(Request $request)
    {
        return view('policy');
    }
}
