@extends('layouts.app')

@section('title', '登入')

@push('styles')
    <style>
        #login-hr {
            border: 0;
            height: 1px;
            background-color: black;
            margin: 20px 0;
        }
    </style>

@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0">
                    <div class="card-body">
                        <h3 class="text-center mb-4">登入帳號</h3>

                        <hr id="login-hr">

                        <form method="POST" action="{{ route('login') }}">
                            @csrf


                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">電子郵件</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{!! $message !!}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">密碼</label>
                                <div class="col-md-6">
                                    <div class="input-group @error('password') is-invalid @enderror">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="d-flex justify-content-end">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                記住我
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary w-100 mb-2">
                                        登入
                                    </button>
                                    
                                    @if (Route::has('password.request'))
                                        <div class="text-center">
                                            <a class="text-decoration-none" href="{{ route('password.request') }}">
                                                忘記密碼？
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // 密碼顯示切換
            $('#togglePassword').click(function () {
                const password = $('#password');
                const icon = $(this).find('i');

                if (password.attr('type') === 'password') {
                    password.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    password.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            // 表單提交前檢查密碼和處理學號
            $('form').on('submit', function (e) {
                // 將學號轉為大寫
                const studentIdInput = $('#student_id');
                studentIdInput.val(studentIdInput.val().toUpperCase());

                // 密碼檢查
                const password = $('#password').val();
                const confirmPassword = $('#password-confirm').val();

                let hasError = false;

                // 如果有錯誤，捲動到第一個錯誤的位置
                if (hasError) {
                    const firstError = $('.is-invalid').first();
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 100
                    }, 500);
                }
            });

            // 當密碼輸入框值改變時，重置錯誤狀態
            $('#password').on('input', function () {
                $(this).removeClass('is-invalid');
                $('#password-length-error').remove();
            });

            $('#password-confirm').on('input', function () {
                $(this).removeClass('is-invalid');
                $('#password-confirm-error').remove();
            });

            // 登入鎖定倒數計時器
            const timerElement = document.getElementById('lockout-timer');
            if (timerElement) {
                let seconds = parseInt(timerElement.textContent);
                const countdownTimer = setInterval(() => {
                    seconds--;
                    timerElement.textContent = seconds;
                    
                    if (seconds <= 0) {
                        clearInterval(countdownTimer);
                        location.reload(); // 時間到自動重新整理頁面
                    }
                }, 1000);
            }
        });
    </script>
@endpush