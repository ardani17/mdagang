<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::query();

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['role'])) {
            $query->where('role', $this->filters['role']);
        }

        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($this->filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Email',
            'Telepon',
            'Departemen',
            'Posisi',
            'Role',
            'Status',
            'Email Terverifikasi',
            'Terakhir Login',
            'Tanggal Dibuat'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone,
            $user->department,
            $user->position,
            $this->getRoleText($user->role),
            $user->is_active ? 'Aktif' : 'Tidak Aktif',
            $user->email_verified_at ? 'Ya' : 'Tidak',
            $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Belum pernah',
            $user->created_at->format('d/m/Y H:i')
        ];
    }

    private function getRoleText($role): string
    {
        return $role === 'administrator' ? 'Administrator' : 'User';
    }
}