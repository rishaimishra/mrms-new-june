<?php

namespace App\Exports;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\PropertySanitationType;
use App\Models\PropertyWindowType;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyUse;
use App\Models\PropertyZones;

class WaybillExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithTitle, WithEvents
{
    protected $propertiesQuery;
    protected $batch;
    protected $ward;
    protected $rowIndex = 0; 

    public function __construct(Collection $propertiesQuery,$batch, $ward)
    {
        $this->propertiesQuery = $propertiesQuery;
        $this->batch = $batch;
        // dd($this->batch);
        $this->ward = $ward;
        // dd($this->ward);
    }
    public function collection()
    {
        // dd($this->propertiesQuery);
        return $this->propertiesQuery;
    }
    public function map($row): array
    {
        $transactions = [];
        
        $payee_name = [];
        $date_of_payment = [];
        
        $assessment_year = $row->assessment->created_at->format('Y');
        $sum=0;
        foreach($row->payments as  $pay)
        {
            if ($assessment_year == $pay->created_at->format('Y')) {
                $sum += $pay->amount;
            }
            
            array_push($transactions, $pay->payment_type);
            array_push($payee_name, $pay->payee_name);
            array_push($date_of_payment, $pay->created_at);
        }
        $amount_paid = $sum > 0 ? $sum : 0;
        if (empty($date_of_payment)) {
            $date_of_payment = 'N/A';
        } else {
            $date_of_payment_updated = (end($date_of_payment)->format('Y-m-d h:i:s'));
        }
        $this->rowIndex++;
        return [
            $this->rowIndex,
            optional($row->landlord)->first_name,
            $row->id,
            // optional($row->assessment)->created_at ? $row->assessment->created_at->format('Y') : '',
            optional($row->assessment)->property_rate_without_gst,
            
            $amount_paid,
            $row->assessment->property_rate_without_gst + $row->assessment->getPastPayableDue() + $row->assessment->getPastPayableDue() * 0.25 - $sum,
            end($payee_name),
            optional($row->landlord)->mobile_1,
            optional($row->landlord)->street_name,
            optional($row->geoRegistry)->digital_address,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Owner Name',
            'ID',
            'Amount',
            'Amount Paid',
            'Amount Due',
            'RECEIPIENT Name',
            'Contact',
            // 'Year',
            'Address',
            'Location',
            'Sign'
            
            
        ];
    }
    public function title(): string
    {
        return "WAYBILL FOR BATCH {$this->batch} WARD {$this->ward}";
        // return "TEST TITLE";
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Insert a new row for the title
                $sheet->insertNewRowBefore(1, 1);
                
                // Merge cells for the title
                $sheet->mergeCells('A1:J1');
                
                // Set the title text
                $sheet->setCellValue('A1', $this->title());
                
                // Apply styling to the title
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
