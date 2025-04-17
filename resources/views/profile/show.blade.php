@extends('layouts.app')

@section('title', '個人資料')

@push('styles')
    <style>
        #profile-hr {
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
                        <h3 class="text-center mb-4">個人資料</h3>

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <hr id="profile-hr">

                        <div class="row mb-4">
                            <div class="col-md-4 text-md-end">
                                <strong>姓名：</strong>
                            </div>
                            <div class="col-md-6">
                                {{ $user->name }}
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4 text-md-end">
                                <strong>學號：</strong>
                            </div>
                            <div class="col-md-6">
                                {{ $user->student_id }}
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4 text-md-end">
                                <strong>電子郵件：</strong>
                            </div>
                            <div class="col-md-6">
                                {{ $user->email }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 offset-md-4">
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary me-2">
                                    編輯個人資料
                                </a>
                            </div>
                        </div>

                        <hr id="profile-hr">

                        <!-- 刪除帳號區塊 -->
                        <div class="row mt-4">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                    刪除帳號
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除帳號確認視窗 -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">確認刪除帳號</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('profile.delete') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="text-danger">警告：此操作無法復原！</p>
                        <div class="mb-3">
                            <label for="password" class="form-label">請輸入密碼確認：</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">確認刪除</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // 如果有錯誤訊息，自動顯示刪除確認視窗
        @if($errors->has('password'))
            $('#deleteAccountModal').modal('show');
        @endif
    });
</script>
@endpush