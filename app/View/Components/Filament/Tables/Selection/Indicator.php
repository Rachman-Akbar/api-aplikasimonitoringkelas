<?php

namespace App\View\Components\Filament\Tables\Selection;

use Illuminate\View\Component;

class Indicator extends Component
{
    public function __construct(
        public $allSelectableRecordsCount,
        public $deselectAllRecordsAction = 'deselectAllRecords',
        public $end = null,
        public $page = null,
        public $selectAllRecordsAction = 'selectAllRecords',
        public $selectCurrentPageOnly = false,
        public $selectedRecordsCount,
        public $selectedRecordsPropertyName = 'selectedRecords',
    ) {}

    public function render()
    {
        return view('components.filament.tables.selection.indicator');
    }
}