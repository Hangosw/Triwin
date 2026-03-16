@extends('layouts.app')

@section('title', 'Thêm Quyền - HRM')

@section('content')
    <div class="page-header">
        <h1>Thêm Quyền mới</h1>
        <p>Thiết lập tên Quyền</p>
    </div>

    <div class="card">
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label">Tên Quyền <span style="color:red;">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Nhập tên Quyền (vd: create user)" required>
                @error('name')
                    <div style="color: red; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">Lưu Quyền</button>
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection
