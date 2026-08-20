<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FirstSheetImport implements WithMultipleSheets
{
    public function __construct(private object $import)
    {
    }

    public function sheets(): array
    {
        return [
            0 => $this->import,
        ];
    }
}
