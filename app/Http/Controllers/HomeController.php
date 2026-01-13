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
    public function services(Request $request)
    {
        return view('services');
    }
    public function prices(Request $request)
    {
        return view('prices');
    }
    public function faq(Request $request)
    {
        return view('faq');
    }
    public function contact(Request $request)
    {
        return view('contact');
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
