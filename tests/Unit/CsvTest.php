<?php

namespace Tests\Unit;

use App\Support\Csv;
use PHPUnit\Framework\TestCase;

class CsvTest extends TestCase
{
    public function test_it_neutralizes_spreadsheet_formula_cells(): void
    {
        $this->assertSame(
            ["'=SUM(A1:A2)", "'+1", "'-1", "'@command", 'ordinary'],
            Csv::sanitize(['=SUM(A1:A2)', '+1', '-1', '@command', 'ordinary'])
        );
    }
}
