<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Pengguna;
use App\Models\Aplikasi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $data = [
            'total_admin' => Pengguna::where('role', 'admin')->count(),
            'total_aplikasi' => Aplikasi::count(),
            'aplikasi_aktif' => Aplikasi::where('status_pemakaian', 'Aktif')->count(),
            'aplikasi_tidak_aktif' => Aplikasi::where('status_pemakaian', '!=', 'Aktif')->count(),
            'log_aktivitas' => LogAktivitas::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ];

        $admins = Pengguna::where('role', 'admin')->get();
        $adminIds = $admins->pluck('id_user');

        // Load all relevant logs in one query instead of N*3 queries
        $logs = LogAktivitas::whereIn('user_id', $adminIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        $activeAdmins = $admins->map(function ($admin) use ($logs) {
            $adminLogs = $logs->get($admin->id_user, collect());

            $lastLogin = $adminLogs->where('aktivitas', 'Login')->first();
            $lastLogout = $adminLogs->where('aktivitas', 'Logout')->first();
            $lastActivity = $adminLogs->first();

            $isOnline = $lastLogin && (!$lastLogout || $lastLogin->created_at > $lastLogout->created_at);

            return [
                'nama' => $admin->nama,
                'email' => $admin->email,
                'last_activity' => $lastActivity ? $lastActivity->created_at : null,
                'last_action' => $lastActivity ? $lastActivity->aktivitas : 'No activity',
                'status' => $isOnline ? 'Online' : 'Offline',
                'sort_order' => $isOnline ? 0 : 1,
            ];
        })->sortBy('sort_order')->values();

        $data['admin_aktif'] = $activeAdmins;

        return view('super-admin.dashboard', $data);
    }
}
