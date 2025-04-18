@extends('layouts.app')

@section('title', '編輯個人資料')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- 個人資料表單 -->
            <div class="card mb-4">
                <div class="card-header">編輯個人資料</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">姓名</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">電子郵件</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="student_id" class="form-label">學號</label>
                            <input type="text" class="form-control @error('student_id') is-invalid @enderror"
                                id="student_id" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                                required>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">更新資料</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 修改密碼區塊 -->
            <div class="d-grid">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updatePasswordModel">
                    更改密碼
                </button>
            </div>

            <!-- 修改密碼確認視窗 -->
            <div class="modal fade" id="updatePasswordModel" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">更改密碼</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('profile.password.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">目前密碼</label>
                                    <input type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">新密碼</label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                        id="password" name="new_password" required>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">確認新密碼</label>
                                    <input type="password" class="form-control" 
                                        id="new_password_confirmation" 
                                        name="new_password_confirmation" 
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                <button type="submit" class="btn btn-primary">確認更改</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <hr>

            <!-- 刪除帳號區塊 -->
            <div class="d-grid">
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    刪除帳號
                </button>
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
                                    <label for="delete_password" class="form-label">請輸入密碼確認：</label>
                                    <input type="password" class="form-control @error('delete_password') is-invalid @enderror"
                                        id="delete_password" name="delete_password" required>
                                    @error('delete_password')
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
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // 如果有修改密碼的錯誤訊息，顯示修改密碼視窗
            @if($errors->has('current_password') || $errors->has('new_password'))
                $('#updatePasswordModel').modal('show');
            @endif

            // 如果有刪除帳號的錯誤訊息，顯示刪除確認視窗
            @if($errors->has('delete_password'))
                $('#deleteAccountModal').modal('show');
            @endif
        });
    </script>
@endpush