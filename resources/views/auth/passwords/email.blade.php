@extends('layouts.app')

@section('title', '重設密碼')

@push('styles')
    <style>
        #reset-hr {
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
                        <h3 class="text-center mb-4">重設密碼</h3>

                        @if (session('status'))
                            <div class="alert alert-success text-center" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <h6 class="text-center mb-4">請輸入帳號的電子郵件，我們將會發送重設密碼連結到您的信箱。</h6>

                        <hr id="reset-hr">

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">電子郵件</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{!! $message !!}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        發送
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
        $(document).ready(function() {
            const timerElement = document.getElementById('lockout-timer');
            if (timerElement) {
                let seconds = parseInt(timerElement.textContent);
                if (seconds > 0) {
                    const countdownTimer = setInterval(() => {
                        seconds--;
                        timerElement.textContent = seconds;
                        
                        if (seconds <= 0) {
                            clearInterval(countdownTimer);
                            location.reload();
                        }
                    }, 1000);
                }
            }
        });
    </script>
@endpush