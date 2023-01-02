<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        $products =  Product::all();
        $products = $products->toArray();
        $data = [];
        for ($i = 0; $i < count($products); $i++) {
            $data[$i]['id'] = $products[$i]['id'];
            $data[$i]['name'] = $products[$i]['name'];
            $data[$i]['type'] = $products[$i]['type'];
            $data[$i]['description'] = $products[$i]['description'];
            $data[$i]['price'] = $products[$i]['price'];
            $data[$i]['quantity'] = $products[$i]['quantity'];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Type',
            'Description',
            'Price',
            'Quantity'
        ];
    }
}
