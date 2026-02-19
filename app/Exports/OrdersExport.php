<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrdersExport implements FromCollection
{
    public function __construct(protected $orders){}

    public function collection()
    {
        return $this->orders->map(function($o){

            return [
                'ID'=>$o->id,
                'Cliente'=>$o->user->name ?? 'Invitado',
                'Total'=>$o->total,
                'Estado'=>$o->status,
                'Fecha'=>$o->created_at
            ];

        });
    }
}