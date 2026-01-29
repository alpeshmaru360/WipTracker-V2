<?php

namespace App\Imports;

use App\Models\ProductBOMItem;
use App\Models\ProductsOfProjects;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\DuplicateBOMRowException;


class ProductBOMImport implements ToModel, WithHeadingRow
{
    private $productId;

    public function __construct($productId){
        $this->productId = $productId;
    }

    public function model(array $row){
        // Validate headers
        $requiredHeaders = ['item_description', 'wilo_article_number', 'item_qty'];
        $rowHeaders = array_keys($row);

        foreach ($requiredHeaders as $header) {
            if (!in_array($header, $rowHeaders)) {
                throw new \Exception('Please provide a valid format file.');
            }
        }      
        // Skip empty rows
        if (
            empty(trim($row['item_description'])) &&
            empty(trim($row['wilo_article_number'])) &&
            empty(trim($row['item_qty']))
        ) {
            return null; // <-- prevents blank rows being saved
        }
        // Normalization function for non-standard BOM values
        $normalize = function ($value) {
            $invalidValues = ['', null, 0, '0', '-', 'n/a', 'N/A'];
            return in_array(trim($value), $invalidValues, true) ? '-' : $value;
        };
        $product = ProductsOfProjects::with('projects')->find($this->productId);
        if (!$product) {
            return null; // Skip if product not found
        }

        // -------------------------------------------
        //  DUPLICATE CHECK PREVENTION
        // -------------------------------------------
        $articleNo = $normalize($row['wilo_article_number']);
        $description = trim($row['item_description']);

        $duplicate = ProductBOMItem::where('product_id', $this->productId)
            ->where('item_desc', $description)
            ->where('wilo_article_no', $articleNo)
            ->exists();

        if ($duplicate) {
            throw new DuplicateBOMRowException("Duplicate item found: Same Item Description & Item Article Number exist in the uploaded Excel sheet.");
        }
        // -------------------------------------------

        return new ProductBOMItem([
            'item_desc' => $description,
            'wilo_article_no' => $articleNo,
            'item_qty' => $row['item_qty'],
            'product_id' => $this->productId,
            'project_id' => $product->project_id,
            'cart_model_name' => $product->cart_model_name,
            'quotation_no' => $product->quotation_number,
            'full_article_no' => $product->full_article_number,
            'product_qty' => $product->qty,
            'total_required_qty' => $row['item_qty'] * $product->qty, // Calculated field
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
