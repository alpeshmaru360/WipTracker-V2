<?php

namespace App\Imports;

use App\Models\StockMasterModule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Handle missing or invalid data gracefully
        $articleNumber = isset($row['art_no']) && trim($row['art_no']) !== '' ? (string) $row['art_no'] : 'N/A';
        $itemDesc = isset($row['product_name']) && trim($row['product_name']) !== '' ? (string) $row['product_name'] : 'Unknown';
        $totalQty = isset($row['total_qty']) && is_numeric($row['total_qty']) ? (int) $row['total_qty'] : 0;
        $minimumRequiredStockAlert = isset($row['minimum_required_stock_alert']) && is_numeric($row['minimum_required_stock_alert']) ? (int) $row['minimum_required_stock_alert'] : 0;
        $etaWeeks = isset($row['eta_weeks']) && is_numeric($row['eta_weeks']) ? (int) $row['eta_weeks'] : 0;

        // Skip unnecessary empty rows
        if ($articleNumber === 'N/A' && $itemDesc === 'Unknown' && $totalQty === 0) {
            return null;
        }
        // Check if record already exists
        $existingStock = StockMasterModule::whereRaw(
                "CONVERT(article_number USING utf8mb4) = ? AND CONVERT(item_desc USING utf8mb4) = ?", 
                [$articleNumber, $itemDesc]
            )->first();

        if ($existingStock) {
            // Update existing row
            $existingStock->qty += $totalQty;
            $existingStock->available_qty += $totalQty;
            $existingStock->minimum_required_qty = $minimumRequiredStockAlert;
            $existingStock->std_time = $etaWeeks;
            $existingStock->save();
            return null; // Stop insert, since we already updated
        }
        // Insert new record
        return new StockMasterModule([
            'article_number' => $articleNumber,
            'item_desc' => $itemDesc,
            'qty' => $totalQty,
            'hold_qty' => 0,
            'available_qty' => $totalQty,
            'minimum_required_qty' => $minimumRequiredStockAlert,
            'std_time' => $etaWeeks,
            'price' => '0.00',
            'total_price' => '0.00',
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
