<?php declare(strict_types=1);

namespace Tests\Unit\App\Imports\Payments;

use App\Imports\Payments\PaymentsImport;
use App\Imports\Payments\Sheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class SheetTest extends TestCase
{
    public function test_parses_real_file_with_thousands_separator(): void
    {
        $spreadsheet = IOFactory::load(__DIR__ . '/blank.xls');

        $this->assertSame(3, $spreadsheet->getSheetCount());

        $import = new PaymentsImport('D', 'E', 'F', $spreadsheet->getSheetCount());
        $sheets = $import->sheets();

        foreach ($spreadsheet->getAllSheets() as $i => $worksheet) {
            $sheets[$i]->array($worksheet->toArray());
        }

        $data = $import->getSheetsData();

        $this->assertCount(3, $data);

        $this->assertCount(172, $data[0]);
        $this->assertCount(231, $data[1]);
        $this->assertCount(113, $data[2]);

        $first = $data[0][0];
        $this->assertSame('1/1', $first[Sheet::ACCOUNT_NUMBER]);
        $this->assertSame(5780.0, $first[Sheet::COST]);
        $this->assertSame(9768.0, $first[Sheet::PAID]);
        $this->assertSame(-3988.0, $first[Sheet::DEBT]);

        $withSuffix = $data[0][1];
        $this->assertSame('1А/1', $withSuffix[Sheet::ACCOUNT_NUMBER]);
        $this->assertSame(4288.0, $withSuffix[Sheet::COST]);

        $last = $data[0][171];
        $this->assertSame('162/1', $last[Sheet::ACCOUNT_NUMBER]);

        $firstSheet2 = $data[1][0];
        $this->assertSame('1/2', $firstSheet2[Sheet::ACCOUNT_NUMBER]);
        $this->assertSame(7120.0, $firstSheet2[Sheet::COST]);
        $this->assertSame(3160.0, $firstSheet2[Sheet::PAID]);
        $this->assertSame(3960.0, $firstSheet2[Sheet::DEBT]);

        $firstSheet3 = $data[2][0];
        $this->assertSame('1/3', $firstSheet3[Sheet::ACCOUNT_NUMBER]);
        $this->assertSame(7420.0, $firstSheet3[Sheet::COST]);
        $this->assertSame(38.0, $firstSheet3[Sheet::PAID]);
        $this->assertSame(7382.0, $firstSheet3[Sheet::DEBT]);

        $complexSuffix = $data[2][1];
        $this->assertSame('2/3', $complexSuffix[Sheet::ACCOUNT_NUMBER]);

        $zeroValues = null;
        foreach ($data[1] as $item) {
            if ($item[Sheet::ACCOUNT_NUMBER] === '158/2') {
                $zeroValues = $item;
                break;
            }
        }
        $this->assertNotNull($zeroValues);
        $this->assertSame(0.0, $zeroValues[Sheet::COST]);
        $this->assertSame(0.0, $zeroValues[Sheet::PAID]);
        $this->assertSame(0.0, $zeroValues[Sheet::DEBT]);

        $this->assertArrayNotHasKey('105 (1/2)/1', array_column($data[0], Sheet::ACCOUNT_NUMBER, Sheet::ACCOUNT_NUMBER));
    }
}