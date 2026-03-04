<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role', '');

        $users = User::when($role, function ($query) use ($role) {
            return $query->where('role', $role);
        })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.users.index', compact('users', 'role'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,teacher,student',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Tạo user thành công');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'role'     => 'required|in:admin,teacher,student',
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        // Chỉ update password nếu có nhập
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật thành công');
    }

    public function destroy(User $user)
    {
        // Không cho xóa chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập');
        }

        // Không cho xóa admin
        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể xóa tài khoản admin');
        }

        // Kiểm tra ràng buộc khóa ngoại
        if ($user->role === 'teacher' && $user->courses()->count() > 0) {
            return back()->with('error', 'Không thể xóa giảng viên đang phụ trách khóa học');
        }

        if ($user->role === 'student') {
            // Xóa các bản ghi liên quan trong bảng trung gian
            $user->courses()->detach();
            $user->lessons()->detach();
            $user->quizResults()->delete();
        }

        $user->delete();
        return back()->with('success', 'Đã xóa user');
    }
}
