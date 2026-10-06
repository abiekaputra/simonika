<?php

namespace App\Services;

use App\Mail\EmailUpdateNotification;
use App\Mail\NewAdminCredentials;
use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use LogicException;

class AdminService
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function create(array $data): Pengguna
    {
        $plainPassword = Str::password(16);

        return DB::transaction(function () use ($data, $plainPassword) {
            $admin = Pengguna::create([
                ...$data,
                'password' => Hash::make($plainPassword),
                'role' => 'admin',
            ]);
            Mail::to($admin->email)->send(new NewAdminCredentials(
                $admin->nama,
                $admin->email,
                $plainPassword
            ));
            $this->logger->record('Admin', 'Add Admin', 'create', "Added admin '{$admin->email}'");

            return $admin;
        });
    }

    public function update(Pengguna $admin, array $data): Pengguna
    {
        return DB::transaction(function () use ($admin, $data) {
            $oldEmail = $admin->email;
            if ($data['email'] !== $oldEmail) {
                Mail::to($data['email'])->send(new EmailUpdateNotification(
                    $data['nama'],
                    $data['email'],
                    $oldEmail
                ));
            }

            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $admin->update($data);
            $this->logger->record('Admin', 'Update Admin', 'update', "Updated admin '{$admin->email}'");

            return $admin->refresh();
        });
    }

    public function delete(Pengguna $admin): void
    {
        if ($admin->isSuperAdmin()) {
            throw new LogicException('Super admin account cannot be deleted.');
        }

        DB::transaction(function () use ($admin) {
            $email = $admin->email;
            $admin->delete();
            $this->logger->record('Admin', 'Delete Admin', 'delete', "Deleted admin '{$email}'");
        });
    }
}
