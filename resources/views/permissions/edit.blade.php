@extends('layouts.app')

@section('title', 'Cập nhật Quyền - HRM')

@section('content')
    <div class="page-header">
        <h1>Cập nhật Quyền</h1>
        <p>Chỉnh sửa thông tin Quyền</p>
    </div>

    <div class="card">
        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label">Tên Quyền <span style="color:red;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $permission->name) }}" required>
                @error('name')
                    <div style="color: red; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection
