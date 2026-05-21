<?php

namespace App\Http\Controllers;

use App\Models\ImportBank;
use App\Models\OcrHistory;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return back()->with('success', 'Profile berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui profile: ' . $e->getMessage());
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini tidak sesuai');
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Password berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui password: ' . $e->getMessage());
        }
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $user = auth()->user();

            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Store new photo
            $path = $request->file('photo')->store('profile-photos', 'public');

            $user->update(['photo' => $path]);

            return back()->with('success', 'Foto profile berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload foto: ' . $e->getMessage());
        }
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        try {
            $user = auth()->user();

            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->update(['photo' => null]);

            return back()->with('success', 'Foto profile berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus foto: ' . $e->getMessage());
        }
    }

    /**
     * Delete account
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = auth()->user();

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password tidak sesuai');
        }

        // Check if user is household owner
        if ($user->household && $user->household->owner_id === $user->id) {
            $memberCount = \App\Models\User::where('household_id', $user->household_id)
                ->where('id', '!=', $user->id)
                ->count();

            if ($memberCount > 0) {
                return back()->with('error', 'Tidak dapat menghapus akun. Transfer ownership household terlebih dahulu');
            }
        }

        try {
            // PDP F2 & F3: hapus semua file fisik milik user sebelum delete akun

            // Foto profil
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // File OCR (gambar struk) — PDP F3
            OcrHistory::where('user_id', $user->id)
                ->whereNotNull('image_path')
                ->each(function (OcrHistory $ocr) {
                    if ($ocr->image_path && Storage::exists($ocr->image_path)) {
                        Storage::delete($ocr->image_path);
                    }
                });

            // File bank statement import yang belum terhapus — PDP F3
            ImportBank::where('user_id', $user->id)
                ->whereNotNull('file_path')
                ->each(function (ImportBank $import) {
                    if ($import->file_path && Storage::exists($import->file_path)) {
                        Storage::delete($import->file_path);
                    }
                });

            // G3: log penghapusan akun sebelum user di-delete
            SecurityLog::record('account_deleted', 'high', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            auth()->logout();
            $user->delete();

            return redirect()->route('login')->with('success', 'Akun berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }
}
