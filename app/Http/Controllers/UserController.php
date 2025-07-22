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
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->paginate($perPage, ['*'], 'page', $page);

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
