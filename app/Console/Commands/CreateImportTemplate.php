<?php

namespace App\Console\Commands;

use App\Exports\ImportTemplateExport;
use App\Support\ImportTemplateDefinitions;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;

class CreateImportTemplate extends Command
{
    protected $signature = 'import:create-template {type}';
    protected $description = 'Create an XLSX import template with Data and Panduan sheets';

    public function handle(): int
    {
        $type = (string) $this->argument('type');

        try {
            $definition = ImportTemplateDefinitions::get($type);
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $path = 'templates/' . $definition['filename'];
        Excel::store(new ImportTemplateExport($type), $path);
        $this->info('Template created: storage/app/' . $path);

        return self::SUCCESS;
    }
}
