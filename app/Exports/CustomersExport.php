<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function collection()
    {
        $query = Customer::query();
        
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($this->filters['status'] === 'inactive') {
                $query->where('is_active', false);
            } elseif ($this->filters['status'] === 'vip') {
                $query->where('type', 'business')->where('is_active', true);
            }
        }
        
        return $query->get();
    }
    
    public function headings(): array
    {
        return [
            'Kode',
            'Nama',
            'Email',
            'Telepon',
            'Alamat',
            'Kota',
            'Kode Pos',
            'Tipe',
            'NPWP',
            'Limit Kredit',
            'Saldo Tertunggak',
            'Status',
            'Catatan',
            'Tanggal Dibuat'
        ];
    }
    
    public function map($customer): array
    {
        return [
            $customer->code,
            $customer->name,
            $customer->email,
            $customer->phone,
            $customer->address,
            $customer->city,
            $customer->postal_code,
            $customer->type === 'business' ? 'Bisnis' : 'Individu',
            $customer->tax_id,
            $customer->credit_limit,
            $customer->outstanding_balance,
            $customer->is_active ? 'Aktif' : 'Tidak Aktif',
            $customer->notes,
            $customer->created_at->format('d/m/Y H:i')
        ];
    }
}