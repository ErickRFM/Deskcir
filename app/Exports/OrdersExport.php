<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\CarbonInterface;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithCustomStartCell, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStyles, WithEvents, WithDrawings
{
    public function __construct(protected $orders)
    {
        $this->orders = collect($orders)->values();
    }

    public function collection()
    {
        return $this->orders;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function headings(): array
    {
        return [
            'Pedido',
            'Cliente',
            'Metodo de pago',
            'Total',
            'Estado',
            'Fecha',
        ];
    }

    public function map($order): array
    {
        /** @var Order $order */
        return [
            $order->id,
            optional($order->user)->name ?? 'Invitado',
            ucfirst(str_replace('_', ' ', (string) $order->payment_method)),
            (float) $order->total,
            ucfirst((string) $order->status),
            $order->created_at instanceof CarbonInterface
                ? ExcelDate::dateTimeToExcel($order->created_at)
                : null,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY . ' hh:mm',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            6 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0B6F81'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastDataRow = max(7, 6 + $this->orders->count());
                $totalAmount = (float) $this->orders->sum(fn ($order) => (float) $order->total);
                $deliveredCount = $this->orders->where('status', 'entregado')->count();
                $pendingCount = $this->orders->where('status', '!=', 'entregado')->count();

                $sheet->mergeCells('B1:F1');
                $sheet->mergeCells('B2:F2');

                $sheet->setCellValue('B1', 'Deskcir | Reporte Ejecutivo de Ventas');
                $sheet->setCellValue('B2', 'Exportacion corporativa con resumen comercial, pedidos registrados y trazabilidad de entrega.');

                $sheet->setCellValue('A3', 'Documento');
                $sheet->setCellValue('B3', 'Ventas');
                $sheet->setCellValue('C3', 'Generado');
                $sheet->setCellValue('D3', now()->format('d/m/Y H:i'));
                $sheet->setCellValue('E3', 'Pedidos');
                $sheet->setCellValue('F3', $this->orders->count());

                $sheet->setCellValue('A4', 'Entregados');
                $sheet->setCellValue('B4', $deliveredCount);
                $sheet->setCellValue('C4', 'Pendientes');
                $sheet->setCellValue('D4', $pendingCount);
                $sheet->setCellValue('E4', 'Ingresos');
                $sheet->setCellValue('F4', $totalAmount);

                $sheet->getStyle('B1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '0D2438']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle('B2')->applyFromArray([
                    'font' => ['size' => 11, 'color' => ['rgb' => '5F778B']],
                    'alignment' => ['wrapText' => true],
                ]);

                $sheet->getStyle('A3:F4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4F9FC'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D8E5ED'],
                        ],
                    ],
                ]);

                $sheet->getStyle('E4:F4')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                $sheet->getStyle("A6:F{$lastDataRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E1EBF2'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle("A7:F{$lastDataRow}")->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '17374D']],
                ]);

                $sheet->freezePane('A7');
                $sheet->setAutoFilter("A6:F{$lastDataRow}");
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(6)->setRowHeight(22);
            },
        ];
    }

    public function drawings(): array
    {
        $logoPath = public_path('img/logo.png');

        if (! file_exists($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Deskcir');
        $drawing->setDescription('Deskcir Logo');
        $drawing->setPath($logoPath);
        $drawing->setHeight(42);
        $drawing->setCoordinates('A1');

        return [$drawing];
    }
}
