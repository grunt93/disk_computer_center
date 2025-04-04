@extends('layouts.app')

@section('title', '註冊')

@push('styles')
    <style>
        #register-hr {
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
                        <h3 class="text-center mb-4">註冊新帳號</h3>

                        <hr id="register-hr">

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">姓名</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                    <div class="form-text text-muted">
                                        <small><i class="bi bi-info-circle"></i> 請使用真實姓名，以便系統管理</small>
                                    </div>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="student_id" class="col-md-4 col-form-label text-md-end">學號</label>
                                <div class="col-md-6">
                                    <input id="student_id" type="text"
                                        class="form-control @error('student_id') is-invalid @enderror" name="student_id"
                                        value="{{ old('student_id') }}" required autocomplete="student_id">
                                    @error('student_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">電子郵件</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">密碼</label>
                                <div class="col-md-6">
                                    <div class="input-group @error('password') is-invalid @enderror">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            name="password"
                                            required 
                                            autocomplete="new-password">
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
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-end">確認密碼</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="password-confirm" type="password" class="form-control"
                                            name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        註冊
                                    </button>
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

                // 檢查密碼長度
                if (password.length < 8) {
                    e.preventDefault();
                    hasError = true;
                    $('#password').addClass('is-invalid');
                    if (!$('#password-length-error').length) {
                        $('#password').parent().after(
                            '<span id="password-length-error" class="invalid-feedback d-block">' +
                            '<strong>密碼至少需要8個字元</strong>' +
                            '</span>'
                        );
                    }
                }

                // 檢查密碼是否相符
                if (password !== confirmPassword) {
                    e.preventDefault();
                    hasError = true;
                    $('#password-confirm').addClass('is-invalid');
                    if (!$('#password-confirm-error').length) {
                        $('#password-confirm').parent().after(
                            '<span id="password-confirm-error" class="invalid-feedback d-block">' +
                            '<strong>密碼與確認密碼不相符</strong>' +
                            '</span>'
                        );
                    }
                }

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
        });
    </script>
@endpush