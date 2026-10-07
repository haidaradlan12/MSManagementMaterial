<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return view('users.index', compact('users'));
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Akun berhasil disetujui.');
    }

    public function reject(User $user)
    {
        $user->update(['is_approved' => false]);

        return redirect()->back()->with('success', 'Akses akun berhasil dicabut.');
    }

    public function makeAdmin(User $user)
    {
        $user->update([
            'is_admin' => true,
            'is_approved' => true
        ]);

        return redirect()->back()->with('success', 'Berhasil menjadikan akun sebagai Admin.');
    }

    public function removeAdmin(User $user)
    {
        $user->update(['is_admin' => false]);

        return redirect()->back()->with('success', 'Berhasil mencabut status Admin dari akun ini.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'Akun berhasil dihapus.');
    }
}
