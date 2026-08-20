<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TemplateDataSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(private array $headers, private array $samples)
    {
    }

    public function array(): array
    {
        return $this->samples;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function title(): string
    {
        return 'Data';
    }
}
