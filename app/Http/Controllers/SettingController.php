<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display settings
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'mata_uang' => 'nullable|string|max:10',
            'format_tanggal' => 'nullable|string|max:20',
            'zona_waktu' => 'nullable|string|max:50',
            'bahasa' => 'nullable|string|max:10',
            'notifikasi_email' => 'nullable|boolean',
            'notifikasi_push' => 'nullable|boolean',
            'notifikasi_anggaran' => 'nullable|boolean',
            'notifikasi_tabungan' => 'nullable|boolean',
            'notifikasi_hutang' => 'nullable|boolean',
            'tema' => 'nullable|in:light,dark,auto',
        ]);

        try {
            $household_id = auth()->user()->household_id;

            foreach ($request->except('_token', '_method') as $key => $value) {
                Setting::updateOrCreate(
                    [
                        'household_id' => $household_id,
                        'key' => $key,
                    ],
                    [
                        'value' => $value ?? '0',
                    ]
                );
            }

            return back()->with('success', 'Pengaturan berhasil disimpan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Reset settings to default
     */
    public function reset()
    {
        try {
            $household_id = auth()->user()->household_id;

            // Delete all settings for this household
            Setting::where('household_id', $household_id)->delete();

            // Recreate default settings
            $defaults = [
                'mata_uang' => 'IDR',
                'format_tanggal' => 'd/m/Y',
                'zona_waktu' => 'Asia/Jakarta',
                'bahasa' => 'id',
                'notifikasi_email' => '1',
                'notifikasi_push' => '1',
                'notifikasi_anggaran' => '1',
                'notifikasi_tabungan' => '1',
                'notifikasi_hutang' => '1',
                'tema' => 'light',
            ];

            foreach ($defaults as $key => $value) {
                Setting::create([
                    'household_id' => $household_id,
                    'key' => $key,
                    'value' => $value,
                ]);
            }

            return back()->with('success', 'Pengaturan berhasil direset ke default');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reset pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Update preferensi tampilan & PDP (AI opt-out)
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'theme'           => 'nullable|in:light,dark,system',
            'language'        => 'nullable|string|max:10',
            'ai_opt_out'      => 'nullable|boolean',
            'ai_ocr_opt_out'  => 'nullable|boolean',
        ]);

        try {
            $householdId = auth()->user()->household_id;
            $data = [
                'theme'          => $request->input('theme', 'light'),
                'language'       => $request->input('language', 'id'),
                'ai_opt_out'     => $request->boolean('ai_opt_out') ? '1' : '0',
                'ai_ocr_opt_out' => $request->boolean('ai_ocr_opt_out') ? '1' : '0',
            ];

            foreach ($data as $key => $value) {
                \App\Models\Setting::updateOrCreate(
                    ['household_id' => $householdId, 'key' => $key],
                    ['value' => $value]
                );
            }

            return back()->with('success', 'Preferensi berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan preferensi: ' . $e->getMessage());
        }
    }

    /**
     * Get setting value
     */
    public static function get($key, $default = null)
    {
        if (!auth()->check()) {
            return $default;
        }

        $setting = Setting::where('household_id', auth()->user()->household_id)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value
     */
    public static function set($key, $value)
    {
        if (!auth()->check()) {
            return false;
        }

        return Setting::updateOrCreate(
            [
                'household_id' => auth()->user()->household_id,
                'key' => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }
}
