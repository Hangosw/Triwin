<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\NhanVienHopDongFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class NhanVienFileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'nhan_vien_id' => 'required|exists:nhan_viens,id',
            'files.*' => 'required|file|mimes:pdf,doc,docx,jpg,png,jpeg|max:10240', // 10MB limit
        ], [
            'files.*.mimes' => 'File phải là PDF, Word, hoặc Ảnh (jpg, png).',
            'files.*.max' => 'File không được quá 10MB.',
        ]);

        $employee = NhanVien::findOrFail($request->nhan_vien_id);
        $ma = $employee->Ma ?: 'NV_' . $employee->id;
        $uploadCount = 0;

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = 'contract_' . $ma . '_' . uniqid() . '.' . $extension;
                
                // Storage path for contracts
                $path = 'uploads/contracts/' . $ma;
                if (!file_exists(public_path($path))) {
                    mkdir(public_path($path), 0777, true);
                }
                
                $file->move(public_path($path), $filename);
                $fullPath = $path . '/' . $filename;

                NhanVienHopDongFile::create([
                    'NhanVienId' => $employee->id,
                    'FileName' => $originalName,
                    'FilePath' => $fullPath,
                    'FileSize' => File::size(public_path($fullPath)),
                ]);
                $uploadCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Đã tải lên {$uploadCount} file thành công!",
        ]);
    }

    public function delete($id)
    {
        $fileEntry = NhanVienHopDongFile::findOrFail($id);
        
        // Delete physical file
        if (file_exists(public_path($fileEntry->FilePath))) {
            unlink(public_path($fileEntry->FilePath));
        }
        
        // Delete DB record
        $fileEntry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa file thành công!',
        ]);
    }
}
