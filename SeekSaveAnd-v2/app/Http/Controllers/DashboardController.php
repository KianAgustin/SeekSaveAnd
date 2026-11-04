<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $myItems = $user->items()->latest()->get();
        $savedItems = $user->savedItems()->with('user')->latest('saved_items.created_at')->get();

        return view('dashboard.index', compact('user', 'myItems', 'savedItems'));
    }
}
