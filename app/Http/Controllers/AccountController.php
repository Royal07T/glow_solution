<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function edit(Request $request)
    {
        return view('settings.index', [
            'user' => $request->user(),
        ]);
    }
}
