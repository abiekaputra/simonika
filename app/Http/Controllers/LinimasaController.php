<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LinimasaController extends Controller
{
    public function index()
    {
        return view('linimasa.index');
    }
}