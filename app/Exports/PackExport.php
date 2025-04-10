<?php

namespace App\Exports;

use App\Models\ImportLine;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PackExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $pack;
    protected $columns;
    protected $status;

    public function __construct($pack, $columns, $status)
    {
        $this->pack = $pack;
        $this->columns = $columns;
        $this->status = $status;
    }

    public function array(): array
    {
        $query = $this->pack->lines()->with('landlords');

        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        $lines = $query->get();
        $rows = [];

        foreach ($lines as $line) {
            if ($line->landlords->count()) {
                $grouped = $line->landlords->groupBy(function ($landlord) {
                    return $landlord->address . '|' . $landlord->postal_code . '|' . $landlord->city;
                });

                foreach ($grouped as $group) {
                    $first = $group->first();
                    $names = $group->pluck('name')->implode(' | ');

                    $row = ['']; // REF SITE = vide

                    foreach ($this->columns as $col) {
                        $row[] = match ($col) {
                            'landlord' => $names,
                            'landlord_address' => $first->address,
                            'landlord_postal_code' => $first->postal_code,
                            'landlord_city' => $first->city,
                            default => $line->$col ?? '',
                        };
                    }

                    $rows[] = $row;
                }
            } else {
                $row = [''];

                foreach ($this->columns as $col) {
                    $row[] = match ($col) {
                        'landlord', 'landlord_address', 'landlord_postal_code', 'landlord_city' => '',
                        default => $line->$col ?? '',
                    };
                }

                $rows[] = $row;
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return array_merge(
            ['REF SITE'],
            array_map(fn($col) => strtoupper(str_replace('_', ' ', $col)), $this->columns)
        );
    }

    public function styles(Worksheet $sheet)
    {
        $highestCol = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle("A1:{$highestCol}1")->getFont()->setBold(true);

        $sheet->getStyle("A1:{$highestCol}{$highestRow}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->getStyle("A1:{$highestCol}{$highestRow}")
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        return [];
    }
}
