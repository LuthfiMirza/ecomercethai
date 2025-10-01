<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Display the account page for the current visitor.
     */
    public function __invoke(Request $request): View
    {
        return view('pages.account');
    }
}
