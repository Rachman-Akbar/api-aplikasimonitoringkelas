<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TemplateGuideSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(private array $guide)
    {
    }

    public function array(): array
    {
        return $this->guide;
    }

    public function headings(): array
    {
        return ['Kolom', 'Wajib', 'Format', 'Contoh', 'Keterangan'];
    }

    public function title(): string
    {
        return 'Panduan';
    }
}
