<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->paginate(10);

        return ResponseFormatter::success($users, 'Users fetched successfully');
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return ResponseFormatter::error(null, 'User not found', 404);
        }

        return ResponseFormatter::success($user, 'User details fetched');
    }
}
