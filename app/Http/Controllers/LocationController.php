<?php

namespace App\Http\Controllers;

use App\Models\Lga;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function lgas(Request $request)
    {
        $validated = $request->validate([
            'state' => ['required', 'integer', 'exists:states,id'],
        ]);

        return Lga::query()
            ->where('state_id', $validated['state'])
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
