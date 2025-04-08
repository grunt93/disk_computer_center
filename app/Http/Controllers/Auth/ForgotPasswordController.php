<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * 驗證電子郵件
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email'], [
            'email.required' => '請輸入電子郵件',
            'email.email' => '請輸入有效的電子郵件格式'
        ]);
    }

    /**
     * 發送請求過多被鎖回應
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => '重設密碼請求次數太多，請在 <span id="lockout-timer">' . $seconds . '</span> 秒後再試']);
    }

    /**
     * 連結發送回應
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('status', '重設密碼連結已發送到您的信箱');
    }

    /**
     * 連結發送失敗回應
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($response === Password::RESET_THROTTLED) {
            $key = 'password-reset|'.$request->ip();
            $seconds = RateLimiter::availableIn($key);
            
            $message = '重設密碼請求太頻繁，請在 <span id="lockout-timer">' . $seconds . '</span> 秒後再試';
        } else {
            $message = match ($response) {
                Password::INVALID_USER => '找不到使用這個電子郵件的使用者',
                default => '重設密碼連結發送失敗，請稍後再試'
            };
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $message]);
    }

    /**
     * 傳送重設密碼連結
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $key = $this->throttleKey($request);

        // 檢查是否在冷卻時間內
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => '重設密碼請求太頻繁，請在 <span id="lockout-timer">' . $seconds . '</span> 秒後再試']);
        }

        // 記錄這次嘗試
        RateLimiter::hit($key, 60);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * 取得節流鍵值
     */
    protected function throttleKey(Request $request)
    {
        return 'password-reset|'.$request->ip();
    }
}
