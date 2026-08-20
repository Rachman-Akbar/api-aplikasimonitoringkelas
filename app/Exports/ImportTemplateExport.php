<?php

namespace App\Exports;

use App\Exports\Sheets\TemplateDataSheet;
use App\Exports\Sheets\TemplateGuideSheet;
use App\Support\ImportTemplateDefinitions;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportTemplateExport implements WithMultipleSheets
{
    private array $definition;

    public function __construct(string $type)
    {
        $this->definition = ImportTemplateDefinitions::get($type);
    }

    public function sheets(): array
    {
        return [
            new TemplateDataSheet($this->definition['headers'], []),
            new TemplateGuideSheet($this->definition['guide']),
        ];
    }

    public function filename(): string
    {
        return $this->definition['filename'];
    }
}
