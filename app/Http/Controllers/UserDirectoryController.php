<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDirectoryController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->where(function ($query) {
                $query->whereIn('account_status', ['active', 'approved'])
                    ->orWhereNull('account_status');
            })
            ->orderBy('name')
            ->paginate(24);

        return view('users.directory', compact('users'));
    }
}
