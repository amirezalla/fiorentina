<?php

namespace App\Http\Controllers\Admin;

use Botble\ACL\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('id', $q);
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'email']);

        return response()->json([
            'results' => $users->map(function ($u) {
                return [
                    'id'   => $u->id,
                    'text' => $u->name . ' (' . $u->email . ')',
                ];
            })->all(),
        ]);
    }
}
