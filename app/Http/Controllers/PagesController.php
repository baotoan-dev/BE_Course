<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index()
    {
        $title = 'Welcome to Laravel!';
        return view('index', compact('title'));
    }

    public function about()
    {
        $title = 'About Us';
        return view('about', compact('title'));
    }
}
