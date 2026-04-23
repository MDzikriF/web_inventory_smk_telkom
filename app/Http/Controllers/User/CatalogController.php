<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        $items = \App\Models\Item::with(['category', 'unit'])->get();
        return view('user.catalog', compact('items'));
    }
}
