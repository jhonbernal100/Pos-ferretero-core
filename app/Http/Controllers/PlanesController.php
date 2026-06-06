<?php

namespace App\Http\Controllers;

class PlanesController extends Controller
{
    public function index()
    {
        return view('planes.index');
    }
}