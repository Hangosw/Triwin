@extends('layouts.app')

@section('title', 'Thêm Role - HRM')

@section('content')
    <div class="page-header">
        <h1>Thêm Role mới</h1>
        <p>Thiết lập tên Role và các quyền liên quan</p>
    </div>

    <div class="card">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label">Tên Role <span style="color:red;">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Nhập tên Role (vd: HR Manager)" required>
                @error('name')
                    <div style="color: red; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Tích chọn các chức năng (Permissions) cho Role này:</label>
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px; margin-top:10px; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    @foreach($permissions as $p)
                        <label style="display:flex; align-items:center; gap:8px; cursor: pointer;">
                            <input type="checkbox" name="permissions[]" value="{{ $p->name }}">
                            <span style="font-size: 14px;">{{ $p->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">Lưu Role</button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection
