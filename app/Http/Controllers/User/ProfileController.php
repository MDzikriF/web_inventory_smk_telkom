<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('user.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $rules = [
            'name' => 'required|string|max:255',
        ];

        $request->validate($rules);

        $user->name = $request->name;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
