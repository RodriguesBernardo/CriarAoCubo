<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $chartData;
    
    public function __construct(array $chartData)
    {
        $this->chartData = $chartData;
    }
    
    public function array(): array
    {
        $data = [];
        
        if (isset($this->chartData['datasets'])) {
            foreach ($this->chartData['datasets'] as $dataset) {
                foreach ($dataset['data'] as $index => $value) {
                    $data[] = [
                        $this->chartData['labels'][$index],
                        $dataset['label'],
                        $value
                    ];
                }
            }
        } else {
            foreach ($this->chartData['labels'] as $index => $label) {
                $data[] = [
                    $label,
                    $this->chartData['data'][$index] ?? 'N/A'
                ];
            }
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        if (isset($this->chartData['datasets'])) {
            return ['Categoria', 'Grupo', 'Valor'];
        }
        
        return ['Categoria', 'Valor'];
    }
    
    public function title(): string
    {
        return 'Relatório';
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Apply a border to all cells
            'A1:C100' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}