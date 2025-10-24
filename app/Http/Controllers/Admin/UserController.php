<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('created_at', 'desc')->paginate(10);
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'role_id.required' => 'Vui lòng chọn vai trò',
            'role_id.exists' => 'Vai trò không hợp lệ',
        ]);

        $plainPassword = $request->password;
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($plainPassword),
            'role_id' => $validated['role_id'],
        ]);

        // Gửi email nếu được chọn
        if ($request->has('send_email')) {
            try {
                Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Tạo người dùng thành công nhưng không thể gửi email!');
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'Tạo người dùng thành công!');
    }

    public function edit($id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã được sử dụng',
            'role_id.required' => 'Vui lòng chọn vai trò',
            'role_id.exists' => 'Vai trò không hợp lệ',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role_id = $validated['role_id'];

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Không cho phép xóa chính mình
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không thể xóa chính mình!');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công!');
    }

    public function sendCredentials($id)
    {
        $user = User::with('role')->findOrFail($id);
        
        // Tạo mật khẩu mới ngẫu nhiên
        $newPassword = \Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->save();
        
        try {
            Mail::to($user->email)->send(new UserCredentialsMail($user, $newPassword));
            return response()->json(['success' => true, 'message' => 'Đã gửi thông tin đăng nhập!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không thể gửi email!'], 500);
        }
    }
}