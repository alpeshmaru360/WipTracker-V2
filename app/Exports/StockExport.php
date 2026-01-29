<?php

namespace App\Exports;

use App\Models\StockMasterModule; // Adjust the model namespace as per your project
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return StockMasterModule::all()->map(function ($stock) {
            return [
                'ART. NO.' => $stock->article_number,
                'PRODUCT NAME' => $stock->item_desc,
                'Total Qty' => $stock->qty,
                'PO.NO.' => $stock->po_no ?? 'NO PO', // Assuming you have a PO number field; adjust accordingly
                'INBOUND BOE' => $stock->inbound_boe ?? '', // Adjust based on your model
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ART. NO.',
            'PRODUCT NAME',
            'Total Qty',
            'PO.NO.',
            'INBOUND BOE',
        ];
    }
}