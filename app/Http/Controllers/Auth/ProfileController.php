<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(){
        return view('profile.show', ['user'=>Auth::user()]);
    }

    public function edit(){
        return view('profile.edit', ['user'=>Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'student_id' => ['required', 'string', 'max:255', 'unique:users,student_id,' . $user->id],
        ], [
            'name.required' => '請輸入姓名',
            'name.max' => '姓名不能超過 255 個字元',
            'email.required' => '請輸入電子郵件',
            'email.email' => '請輸入有效的電子郵件格式',
            'email.max' => '電子郵件不能超過 255 個字元',
            'email.unique' => '此電子郵件已被使用',
            'student_id.required' => '請輸入學號',
            'student_id.max' => '學號不能超過 255 個字元',
            'student_id.unique' => '此學號已被使用'
        ]);

        /** @var User $user */
        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('status', '個人資料已更新成功');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => '請輸入目前密碼',
            'current_password.current_password' => '目前密碼不正確',
            'password.required' => '請輸入新密碼',
            'password.confirmed' => '新密碼與確認新密碼不相符',
            'password.min' => '密碼至少需要 8 個字元'
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('status', '密碼更新成功');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => '請輸入密碼',
            'password.current_password' => '密碼不正確'
        ]);

        /**
         * @var User $user 
         */
        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect('/')->with('status', '帳號已成功刪除');
    }
}
