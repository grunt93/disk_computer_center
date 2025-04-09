<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/home';

    /**
     * 取得驗證規則
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }

    /**
     * 取得驗證錯誤訊息
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [
            'token.required' => '連結已過期，請重新發送重設密碼請求',
            'email.required' => '請輸入電子郵件',
            'email.email' => '請輸入有效的電子郵件格式',
            'password.required' => '請輸入新密碼',
            'password.confirmed' => '新密碼與確認新密碼不相符',
            'password.min' => '密碼至少需要 8 個字元'
        ];
    }

    /**
     * 重設密碼成功回應
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())
            ->with('status', '密碼重設成功');
    }

    /**
     * 重設密碼失敗回應
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        $message = match ($response) {
            Password::INVALID_TOKEN => '無效的重設密碼連結',
            Password::INVALID_USER => '找不到使用這個電子郵件的使用者',
            default => '密碼重設失敗，請稍後再試'
        };

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $message]);
    }
}
