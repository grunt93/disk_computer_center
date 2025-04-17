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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

