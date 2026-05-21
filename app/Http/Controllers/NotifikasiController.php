<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Notifikasi::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($request->filled('is_read')) {
            $query->where('is_read', $request->is_read === 'true');
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $notifikasi = $query->paginate(20);

        $unreadCount = Notifikasi::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return view('notifikasi.index', compact('notifikasi', 'unreadCount'));
    }

    public function markAsRead(Notifikasi $notifikasi)
    {
        if ($notifikasi->user_id !== auth()->id()) {
            abort(403);
        }

        $notifikasi->markAsRead();

        return back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    public function markAllAsRead()
    {
        Notifikasi::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }

    public function destroy(Notifikasi $notifikasi)
    {
        if ($notifikasi->user_id !== auth()->id()) {
            abort(403);
        }

        $notifikasi->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus');
    }

    public function unreadCount()
    {
        $count = Notifikasi::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function recent()
    {
        $notifikasi = Notifikasi::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($notifikasi);
    }
}
