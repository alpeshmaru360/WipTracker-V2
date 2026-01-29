<?php

namespace App\Exports;

use App\Models\StockBOMPo;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\DB;

class MRFToWarehouseExport implements WithEvents, WithDrawings
{
    protected $productId;
    protected $projectId;
    protected $filterType; // 'inspected', 'not_inspected', or 'all'

    public function __construct($productId, $projectId, $filterType = 'all')
    {
        $this->productId = $productId;
        $this->projectId = $projectId;
        $this->filterType = $filterType;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                // TITLE ROW
                $sheet->mergeCells('B5:K6');
                $sheet->setCellValue('B5', 'Warehouse Material Request Form');
                $sheet->getStyle('B5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 20],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                // HEADER FIELDS
                $sheet->mergeCells('B8:C8');
                $sheet->setCellValue('B8', 'Date:');
                $sheet->mergeCells('D8:H8');
                $sheet->setCellValue('D8', Carbon::now()->format('d-m-Y'));
                $sheet->getStyle('B8:C8')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                ]);
                $sheet->getStyle('D8:H8')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $sheet->mergeCells('B9:C9');
                $sheet->setCellValue('B9', 'Requester Name:');
                $sheet->mergeCells('D9:H9');
                $productionEngineer = User::where('role', 'Production Engineer')->first();
                $sheet->setCellValue('D9', $productionEngineer ? $productionEngineer->name : 'N/A');
                $sheet->getStyle('B9:C9')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                ]);
                $sheet->getStyle('D9:H9')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $sheet->mergeCells('B10:C10');
                $sheet->setCellValue('B10', 'Department:');
                $sheet->mergeCells('D10:H10');
                $sheet->setCellValue('D10', 'Production');
                $sheet->getStyle('B10:C10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                ]);
                $sheet->getStyle('D10:H10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $sheet->mergeCells('J9:J10');
                $sheet->setCellValue('J9', 'MRF #');
                $sheet->mergeCells('K9:L10');
                $sheet->setCellValue('K9', 'N/A');
                $sheet->getStyle('J9:J10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getStyle('K9:L10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // HEADER ROW with word wrap
                $sheet->setCellValue('B11', 'SL#');
                $sheet->setCellValue('C11', 'Article No.');
                $sheet->setCellValue('D11', 'Equipment No./Serial No.');
                $sheet->mergeCells('E11:G11');
                $sheet->setCellValue('E11', 'Description');
                $sheet->setCellValue('H11', 'Quantity');
                $sheet->setCellValue('I11', 'No. of Pallet');
                $sheet->setCellValue('J11', 'PO No.');
                $sheet->mergeCells('K11:L11');
                $sheet->setCellValue('K11', 'Inbound BOE'); // Updated header for Inbound BOE
                $sheet->getStyle('B11:L11')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD3D3D3']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                // DATA ROWS (Dynamic number of items with filtering)
                // Get project details for filtering
                $project = DB::table('projects')->where('id', $this->projectId)->first();
                $project_no = $project ? $project->project_no : null;
                // Base query for BOM items
               $query = StockBOMPo::where('product_id', $this->productId)
                    ->where('project_id', $this->projectId);
                // Apply filtering based on filterType
                if ($this->filterType === 'inspected' && $project_no) {
                    $query->addSelect([
                        'inspection_qty' => DB::table('initial_inspection_data')
                            ->select('quantity')
                            ->whereColumn('po_number', 'stock_bom_po.po_no')
                            ->whereRaw("REPLACE(artical_no, ' ', '') = REPLACE(stock_bom_po.article_no, ' ', '')")
                            ->whereRaw("description LIKE CONCAT('%', stock_bom_po.description, '%')")
                            ->where('project_no', $project_no)
                            ->limit(1)
                    ]);

                    $query->where(function ($mainQuery) use ($project_no) {
                        $mainQuery
                            ->whereExists(function ($subQuery) use ($project_no) {
                                $subQuery->select(DB::raw(1))
                                    ->from('initial_inspection_data')
                                    ->where('project_no', $project_no)
                                    ->whereColumn('po_number', 'stock_bom_po.po_no')
                                    ->whereRaw("REPLACE(artical_no, ' ', '') = REPLACE(stock_bom_po.article_no, ' ', '')")
                                    ->whereRaw("description LIKE CONCAT('%', stock_bom_po.description, '%')");
                            })
                            // Include records where po_no = 'N/A' and is_email_sent = 0
                            ->orWhere(function ($orQuery) {
                                $orQuery->where('po_no', 'N/A')
                                        ->where('is_email_sent', 0);
                            });
                    })
                    // Exclude records where is_email_sent = 1 and mrf_email_sent_date is NOT NULL
                    ->where(function ($exclude) {
                        // $exclude->where('is_email_sent', '!=', 1)
                        $exclude->whereNotIn('is_email_sent', [1, 2])

                                ->orWhereNull('mrf_email_sent_date');
                    });
                }

                elseif ($this->filterType === 'not_inspected' && $project_no) {
                    // Only items that have NOT completed initial inspection
                    $query->whereNotExists(function ($subQuery) use ($project_no) {
                        $subQuery->select(DB::raw(1))
                            ->from('initial_inspection_data')
                            ->where('project_no', $project_no)
                            ->whereColumn('po_number', 'stock_bom_po.po_no')
                            ->whereRaw("REPLACE(artical_no, ' ', '') = REPLACE(stock_bom_po.article_no, ' ', '')")
                            ->whereRaw("description LIKE CONCAT('%', stock_bom_po.description, '%')");
                    })
                    ->where('select_option', '!=', 'stock');
                }

                elseif ($this->filterType === 'from_stock' && $project_no) {
                    // Only items that have from stock only all items
                    $query->where('po_no', 'N/A')->where('is_email_sent', 0);
                }

                $bomItems = $query->get();
                $startRow = 12;
                $sl = 1;
                $totalQuantity = 0;

                foreach ($bomItems as $item) {
                    if ($item->item_quantity != 0) { // Skip items with item_quantity == 0
                        $sheet->setCellValue("B$startRow", $sl++);
                        $sheet->setCellValue("C$startRow", $item->article_no ?? 'N/A');
                        $sheet->setCellValue("D$startRow", 'N/A');
                        $sheet->mergeCells("E$startRow:G$startRow");
                        $description = $item->description ?? '';
                        $sheet->setCellValue("E$startRow", $description);
                        $sheet->getStyle("E$startRow:G$startRow")->applyFromArray([
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_TOP,
                                'wrapText' => true,
                            ],
                        ]);
                        // Calculate approximate row height based on description length
                        $charCount = strlen($description);
                        $baseHeight = 15; // Default row height
                        $lines = ceil($charCount / 30); // Estimate lines based on ~30 chars per line
                        $newHeight = $baseHeight * min($lines, 5); // Cap at 5 lines to avoid excessive height
                        $sheet->getRowDimension($startRow)->setRowHeight($newHeight);
                        // $sheet->setCellValue("H$startRow", $item->total_required_quantity);
                        $sheet->setCellValue("H$startRow", $item->inspection_qty);
                        
                        $sheet->getStyle("H$startRow")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER); // Ensure numeric format
                        // $totalQuantity += $item->total_required_quantity; // Accumulate total
                        $totalQuantity += $item->inspection_qty; 
                        $sheet->setCellValue("I$startRow", 'N/A');
                        $sheet->setCellValue("J$startRow", $item->po_no ?? 'N/A'); // PO No.
                        $sheet->mergeCells("K$startRow:L$startRow");
                        $sheet->setCellValue("K$startRow", $item->boe ?? ''); // Inbound BOE, empty if null
                        $startRow++;
                    }
                }

                // TOTALS (Placed after last data row, merged B to G and K to L)
                $totalRow = $startRow;
                $sheet->mergeCells("B$totalRow:G$totalRow");
                $sheet->setCellValue("B$totalRow", 'Total');
                $sheet->setCellValue("H$totalRow", $totalQuantity); // Set static total
                $sheet->getStyle("H$totalRow")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER); // Ensure numeric format
                $sheet->setCellValue("I$totalRow", '0');
                $sheet->mergeCells("K$totalRow:L$totalRow"); // Merge K and L in total row
                $sheet->getStyle("B$totalRow:L$totalRow")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);
                $sheet->getStyle("H$totalRow:I$totalRow")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);

                // FOOTER (Placed two rows after Total)
                $footerStartRow = $totalRow + 2;
                $sheet->mergeCells("B$footerStartRow:L" . ($footerStartRow + 3))->setCellValue("B$footerStartRow", 'Remarks:');
                $sheet->getStyle("B$footerStartRow:L" . ($footerStartRow + 3))->applyFromArray([
                    'font' => ['size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);
                // Empty row after Remarks
                $emptyRow = $footerStartRow + 4;
                // Subsequent footer sections start after the empty row
                $nextFooterRow = $emptyRow + 1;
                $sheet->mergeCells("B$nextFooterRow:F" . ($nextFooterRow + 3))->setCellValue("B$nextFooterRow", 'Verified by:');
                $sheet->getStyle("B$nextFooterRow:F" . ($nextFooterRow + 3))->applyFromArray([
                    'font' => ['size' => 12, 'bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => [
                        'left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_NONE, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);
                $sheet->mergeCells("B" . ($nextFooterRow + 4) . ":F" . ($nextFooterRow + 7))->setCellValue("B" . ($nextFooterRow + 4), 'Signature & Date');
                $sheet->getStyle("B" . ($nextFooterRow + 4) . ":F" . ($nextFooterRow + 7))->applyFromArray([
                    'font' => ['size' => 12, 'bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => [
                        'left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'top' => ['borderStyle' => Border::BORDER_NONE, 'color' => ['argb' => 'FF000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);
                $sheet->mergeCells("G$nextFooterRow:L" . ($nextFooterRow + 3))->setCellValue("G$nextFooterRow", 'Approved by:');
                $sheet->getStyle("G$nextFooterRow:L" . ($nextFooterRow + 3))->applyFromArray([
                    'font' => ['size' => 12, 'bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => [
                        'left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_NONE, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);
                $sheet->mergeCells("G" . ($nextFooterRow + 4) . ":L" . ($nextFooterRow + 7))->setCellValue("G" . ($nextFooterRow + 4), 'Signature & Date');
                $sheet->getStyle("G" . ($nextFooterRow + 4) . ":L" . ($nextFooterRow + 7))->applyFromArray([
                    'font' => ['size' => 12, 'bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => [
                        'left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'top' => ['borderStyle' => Border::BORDER_NONE, 'color' => ['argb' => 'FF000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);
                $nextFooterRow += 8; // 4 rows for Approved by + 4 rows for Signature & Date
                $sheet->mergeCells("B$nextFooterRow:F" . ($nextFooterRow + 3))->setCellValue("B$nextFooterRow", 'Packed & Prepared by:');
                $sheet->getStyle("B$nextFooterRow:F" . ($nextFooterRow + 3))->applyFromArray([
                    'font' => ['size' => 12, 'bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => [
                        'left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_NONE, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);
                $sheet->mergeCells("B" . ($nextFooterRow + 4) . ":F" . ($nextFooterRow + 7))->setCellValue("B" . ($nextFooterRow + 4), 'Signature & Date');
                $sheet->getStyle("B" . ($nextFooterRow + 4) . ":F" . ($nextFooterRow + 7))->applyFromArray([
                    'font' => ['size' => 12, 'bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => [
                        'left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'top' => ['borderStyle' => Border::BORDER_NONE, 'color' => ['argb' => 'FF000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);
                $sheet->mergeCells("G$nextFooterRow:L" . ($nextFooterRow + 3))->setCellValue("G$nextFooterRow", 'I hereby confirm that the materials I\'ve received are in good condition and are correct as per the above list.');
                $sheet->getStyle("G$nextFooterRow:L" . ($nextFooterRow + 3))->applyFromArray([
                    'font' => ['size' => 12, 'bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_NONE, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);
                $sheet->mergeCells("G" . ($nextFooterRow + 4) . ":L" . ($nextFooterRow + 5))->setCellValue("G" . ($nextFooterRow + 4), 'Received By:');
                $sheet->mergeCells("G" . ($nextFooterRow + 6) . ":L" . ($nextFooterRow + 7))->setCellValue("G" . ($nextFooterRow + 6), 'Date:');
                $sheet->getStyle("G" . ($nextFooterRow + 4) . ":L" . ($nextFooterRow + 7))->applyFromArray([
                    'font' => ['size' => 12, 'bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => [
                        'left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'top' => ['borderStyle' => Border::BORDER_NONE, 'color' => ['argb' => 'FF000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);

                // Apply dark blue border around the entire sheet from A to M
                $lastRow = $nextFooterRow + 7;
                $sheet->getStyle("A1:M$lastRow")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['argb' => 'FF0000FF'],
                        ],
                    ],
                ]);

                // Apply gray fill to cells outside the blue border range
                $maxColumn = 'Z'; // Extend to column Z for simplicity; adjust as needed
                $maxRow = 100;    // Arbitrary max row; adjust based on your needs
                $sheet->getStyle("A1:$maxColumn$maxRow")
                    ->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFD3D3D3'],
                        ],
                    ]);
                // Remove gray fill from within the border range
                $sheet->getStyle("A1:M$lastRow")
                    ->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_NONE,
                        ],
                    ]);


                // Set print area to exclude disabled fields
                $sheet->getPageSetup()->setPrintArea("A1:M$lastRow");

                // TABLE STYLING (Dynamic range) with outer dark border
                $lastDataRow = $totalRow - 1;
                $sheet->getStyle('B11:L' . $lastDataRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                        'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);
                $sheet->getStyle("B$totalRow:L$totalRow")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                        'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
                    ],
                ]);

                // COLUMN WIDTHS
                $widths = ['A' => 2, 'B' => 10, 'C' => 10, 'D' => 12, 'E' => 15, 'F' => 10, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 13, 'K' => 13, 'L' => 2, 'M' => 2];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }
            },
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('wilo_logo');
        $drawing->setDescription('wilo Logo');
        $drawing->setPath(public_path('storage/templates/wilo_logo.png'));
        $drawing->setHeight(90);
        $drawing->setCoordinates('J1');
        return [$drawing];
    }
}
