<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * 中文錯誤提示。
     *
     * @return array
     */
    protected function validationErrors()
    {
        return [
            'email.required' => '請輸入電子郵件',
            'email.email' => '請輸入有效的電子郵件格式',
            'password.required' => '請輸入密碼',
            'password.min' => '密碼至少需要 8 個字元'
        ];
    }

    /**
     * 重寫登入失敗訊息
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => '電子郵件或密碼錯誤'
            ]);
    }

    /**
     * 登入次數過多回應
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => '登入嘗試次數太多，請在 <span id="lockout-timer">' . $seconds . '</span> 秒後再試'
            ]);
    }

    /**
     * 驗證登入嘗試
     *
     * @param Request $request
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|email',
            'password' => 'required|min:8'
        ], $this->validationErrors());
    }
}
