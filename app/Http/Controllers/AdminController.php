<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Pengguna;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AdminController extends Controller
{
    public function __construct(private readonly AdminService $service) {}

    public function index(): View
    {
        return view('admin.index', [
            'admins' => Pengguna::query()->where('role', 'admin')->orderBy('nama')->paginate(20),
        ]);
    }

    public function store(StoreAdminRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $this->service->create($request->validated());

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Admin berhasil ditambahkan.'], 201);
            }

            return to_route('admin.index')->with('success', 'Admin berhasil ditambahkan.');
        } catch (Throwable $exception) {
            Log::error('Failed to create admin account.', ['exception' => $exception]);

            $message = 'Akun admin tidak dapat dibuat. Periksa konfigurasi email lalu coba kembali.';
            if (! $request->expectsJson()) {
                return back()->withInput()->with('error', $message);
            }

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 500);
        }
    }

    public function edit(Pengguna $admin): JsonResponse
    {
        abort_unless($admin->isAdmin(), 404);

        return response()->json(['success' => true, 'data' => $admin]);
    }

    public function update(UpdateAdminRequest $request, Pengguna $admin): JsonResponse|RedirectResponse
    {
        abort_unless($admin->isAdmin(), 404);

        try {
            $updated = $this->service->update($admin, $request->validated());

            if (! $request->expectsJson()) {
                return to_route('admin.index')->with('success', 'Admin berhasil diperbarui.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil diperbarui.',
                'data' => $updated,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to update admin account.', ['exception' => $exception]);

            $message = 'Akun admin tidak dapat diperbarui. Periksa konfigurasi email lalu coba kembali.';
            if (! $request->expectsJson()) {
                return back()->withInput()->with('error', $message);
            }

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 500);
        }
    }

    public function destroy(Pengguna $admin): RedirectResponse
    {
        $this->service->delete($admin);

        return redirect()->route('admin.index')->with('success', 'Admin berhasil dihapus.');
    }
}
