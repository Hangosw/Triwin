<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CauHinhController extends Controller
{
    public function index()
    {
        $configs = \App\Models\SystemConfig::pluck('value', 'key')->toArray();
        $caLamViecs = \App\Models\DmCaLamViec::all();
        $lichLamViecs = \App\Models\CauHinhLichLamViec::orderBy('Thu', 'asc')->get();
        $nhanViens = \App\Models\NhanVien::select('id', 'Ma', 'Ten')->get();

        return view('config.index', compact('configs', 'caLamViecs', 'lichLamViecs', 'nhanViens'));
    }

    public function update(Request $request)
    {
        $hasPermission = auth()->user()->can('Quản lý hệ thống');
        $data = $request->except(['_token', 'company_logo']);

        // Handle text-based configs
        foreach ($data as $key => $value) {
            if ($value === 'on') {
                $value = 1;
            }

            // Save theme and language to the authenticated user
            // This is allowed for all regular users
            if ($key === 'default_theme' || $key === 'default_language') {
                $user = auth()->user();
                if ($user) {
                    $column = ($key === 'default_theme') ? 'theme' : 'language';
                    $user->update([$column => $value]);
                }
            }

            // ONLY update system-wide config if user has permission
            if ($hasPermission) {
                \App\Models\SystemConfig::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        // Handle file upload for company logo - ONLY if has permission
        if ($hasPermission && $request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $filename = 'company_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/company'), $filename);
            $publicPath = 'uploads/company/' . $filename;

            \App\Models\SystemConfig::updateOrCreate(
                ['key' => 'company_logo'],
                ['value' => $publicPath]
            );
        }

        if ($hasPermission) {
            \App\Services\SystemLogService::log('Cập nhật', 'SystemConfig', null, __('Cập nhật cấu hình hệ thống chung'));
        }

        return redirect()->back()->with('success', $hasPermission ? __('Đã lưu cấu hình hệ thống thành công!') : __('Đã cập nhật tùy chọn cá nhân thành công!'));
    }

    public function updateCaLamViec(Request $request)
    {
        $data = $request->validate([
            'ca_lam_viecs' => 'required|array',
            'ca_lam_viecs.*.MaCa' => 'required',
            'ca_lam_viecs.*.TenCa' => 'required',
            'ca_lam_viecs.*.GioVao' => 'required',
            'ca_lam_viecs.*.GioRa' => 'required',
            'ca_lam_viecs.*.BatDauNghi' => 'required',
            'ca_lam_viecs.*.KetThucNghi' => 'required',
            'ca_lam_viecs.*.GhiChu' => 'nullable|string',
            'ca_lam_viecs.*.PhuCapCaDem' => 'nullable|numeric',
        ]);

        foreach ($data['ca_lam_viecs'] as $id => $caData) {
            $dmCa = \App\Models\DmCaLamViec::find($id);
            if ($dmCa) {
                // If the checkbox is checked, it will be in the request data, otherwise it won't be
                $isQuaDem = isset($request->ca_lam_viecs[$id]['LaCaQuaDem']);
                $caData['LaCaQuaDem'] = $isQuaDem ? 1 : 0;
                $caData['PhuCapCaDem'] = $caData['PhuCapCaDem'] ?? 0;

                $dmCa->update($caData);
            }
        }

        \App\Services\SystemLogService::log('Cập nhật', 'DmCaLamViec', null, __('Cập nhật thông tin ca làm việc'));

        return redirect()->back()->with('success', __('Đã cập nhật thông tin ca làm việc thành công!'));
    }

    public function updateLichLamViec(Request $request)
    {
        $data = $request->validate([
            'lich_lam_viecs' => 'required|array',
            'lich_lam_viecs.*.type' => 'required|in:full,half,off',
        ]);

        foreach ($data['lich_lam_viecs'] as $id => $lichData) {
            $lich = \App\Models\CauHinhLichLamViec::find($id);
            if ($lich) {
                $updateData = [];
                switch ($lichData['type']) {
                    case 'full':
                        $updateData = ['CoLamViec' => 1, 'HeSoNgayCong' => 1.0];
                        break;
                    case 'half':
                        $updateData = ['CoLamViec' => 1, 'HeSoNgayCong' => 0.5];
                        break;
                    case 'off':
                        $updateData = ['CoLamViec' => 0, 'HeSoNgayCong' => 0.0];
                        break;
                }
                $lich->update($updateData);
            }
        }

        \App\Services\SystemLogService::log('Cập nhật', 'CauHinhLichLamViec', null, __('Cập nhật cấu hình ngày làm việc'));

        return redirect()->back()->with('success', __('Đã cập nhật cấu hình ngày làm việc thành công!'));
    }
}
