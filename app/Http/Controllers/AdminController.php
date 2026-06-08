<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewAdminCredentials;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Mail\EmailUpdateNotification;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Pengguna::where('role', 'admin')->get();
        return view('admin.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:100',
                'email' => 'required|email|unique:penggunas',
            ]);

            
            $plainPassword = Str::random(8);
            $validated['password'] = Hash::make($plainPassword);
            $validated['role'] = 'admin';

            $admin = Pengguna::create($validated);

            
            Mail::to($validated['email'])->send(new NewAdminCredentials(
                $validated['nama'],
                $validated['email'],
                $plainPassword
            ));

            return response()->json([
                'success' => true,
                'message' => 'Admin added successfully.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $admin = Pengguna::findOrFail($id);
            return response()->json($admin);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Admin not found.'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $admin = Pengguna::findOrFail($id);
            $oldEmail = $admin->email;

            $validated = $request->validate([
                'nama' => 'required|string|max:100',
                'email' => 'required|email|unique:penggunas,email,' . $id . ',id_user',
                'password' => 'nullable|min:6'
            ]);

            
            if ($validated['email'] !== $oldEmail) {
                Mail::to($validated['email'])->send(new EmailUpdateNotification(
                    $validated['nama'],
                    $validated['email'],
                    $oldEmail
                ));
            }

            
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                
                unset($validated['password']);
            }

            $admin->update($validated);

            return response()->json([
                'success' => true,
                'message' => $validated['email'] !== $oldEmail ?
                    'Admin updated and notification sent to new email.' :
                    'Admin updated successfully.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $admin = Pengguna::findOrFail($id);
        $admin->delete();
        return redirect()->route('admin.index')->with('success', 'Admin deleted successfully.');
    }
}
