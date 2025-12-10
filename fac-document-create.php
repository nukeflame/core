<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Shared\Converter;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    private const VALID_PREMIUM_FIELDS = [
        'premium',
        'total sum insured',
        'first loss',
        'top location',
        'limit of indemnity',
        'maximum loss limit',
        'limit of liability',
        'agreed value'
    ];

    /**
     * Generate quotation cover slip as PDF or DOCX
     */
    public function QuotationCoverSlip(Request $request)
    {
        try {
            // Validate required parameters
            $validated = $request->validate([
                'opp_id' => 'required',
                'printout_flag' => 'sometimes|boolean',
            ]);

            // Fetch opportunity data
            $activities = $this->fetchOpportunityData($request->opp_id);

            if ($activities->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Opportunity not found.',
                ], 404);
            }

            // Format activity data
            $formattedActivity = $this->formatActivityData($activities->first());

            // Prepare reinsurer shares
            $shares = $this->prepareReinsurers($request, $formattedActivity);

            // Build request data structure
            $requestData = $this->buildRequestData($request, $formattedActivity, $shares);

            // Prepare customer objects
            $customers = $this->prepareCustomerObjects($requestData);

            // Fetch company information
            $company = Company::firstOrFail();

            // Final data structure
            $data = [
                'company' => $company,
                'customers' => $customers,
                'shares' => $requestData['shares'],
                'unplaced' => $requestData['unplaced'],
                'updated_written_share_total' => $requestData['updated_written_share_total'],
                'stage' => $requestData['stage'],
            ];

            // Generate appropriate output
            if ($request->printout_flag == 1) {
                return $this->generateWordDocument($data);
            } else {
                return $this->generatePdfDocument($data);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('QuotationCoverSlip Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while generating the document.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Fetch opportunity data from database
     */
    private function fetchOpportunityData(string $opportunityId)
    {
        return DB::table('pipeline_opportunities as po')
            ->leftJoin('customers as c', function ($join) {
                $join->on(DB::raw("NULLIF(po.customer_id, '')::INTEGER"), '=', 'c.customer_id');
            })
            ->leftJoin('lead_status as ls', 'po.stage', '=', 'ls.id')
            ->leftJoin('reins_division as rd', 'po.divisions', '=', 'rd.division_code')
            ->leftJoin('classes as cl', 'po.classcode', '=', 'cl.class_code')
            ->leftJoin('business_types as bt', 'po.type_of_bus', '=', 'bt.bus_type_id')
            ->selectRaw('DISTINCT ON (po.opportunity_id) po.*,
                COALESCE(c.name, \'N/A\') AS customer_name,
                ls.status_name as stage,
                rd.division_name as division_name,
                po.cede_premium as cedant_premium,
                po.rein_premium as reinsurer_premium,
                cl.class_name as class_name,
                bt.bus_type_name as type_of_bus')
            ->where('po.opportunity_id', $opportunityId)
            ->get();
    }

    /**
     * Format activity data for use in document
     */
    private function formatActivityData($activity)
    {
        return [
            'customer_id' => $activity->customer_id ?? 'N/A',
            'customer_name' => $activity->customer_name ?? 'N/A',
            'opportunity_id' => $activity->opportunity_id,
            'effective_date' => $activity->effective_date,
            'closing_date' => $activity->closing_date,
            'insured_name' => $activity->insured_name,
            'type_of_bus' => $activity->type_of_bus,
            'currency_code' => $activity->currency_code,
            'contact_name' => $this->decodeJsonField($activity->contact_name),
            'email' => $this->decodeJsonField($activity->email),
            'phone' => $this->decodeJsonField($activity->phone),
            'telephone' => $this->decodeJsonField($activity->telephone),
            'class_name' => $activity->class_name,
            'stageType' => $activity->category_type ?? null,
        ];
    }

    /**
     * Safely decode JSON field
     */
    private function decodeJsonField($field)
    {
        if (empty($field)) {
            return [];
        }

        $decoded = json_decode($field, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Prepare reinsurer shares from request
     */
    private function prepareReinsurers(Request $request, array $formattedActivity)
    {
        // If reinsurers already provided, use them
        if ($request->has('reinsurers')) {
            return $request->reinsurers;
        }

        // Otherwise build from individual arrays
        $customerIds = $request->input('customer_id', []);

        if (empty($customerIds)) {
            return [];
        }

        return collect($customerIds)->map(function ($id, $index) use ($request) {
            return [
                'customer_id' => $id,
                'customer_name' => $request->customer_name[$index] ?? null,
                'customer_email' => $request->customer_email[$index] ?? null,
                'written_share' => $request->written_share[$index] ?? null,
                'contact_name' => ($request->contact_name[$index] ?? null) === 'null' ? null : ($request->contact_name[$index] ?? null),
            ];
        })->toArray();
    }

    /**
     * Build comprehensive request data structure
     */
    private function buildRequestData(Request $request, array $formattedActivity, $shares)
    {
        return [
            'customer_name' => $formattedActivity['customer_name'],
            'customer_id' => $formattedActivity['customer_id'],
            'schedule_details' => $this->decodeScheduleDetails($request->schedule_details),
            'quote_title_intro' => $request->quote_title_intro ?? '',
            'facschedule_details' => $this->decodeScheduleDetails($request->facschedule_details),
            'shares' => $shares,
            'unplaced' => $request->unplaced ?? '',
            'updated_written_share_total' => $request->updated_written_share_total ?? $request->written_share_total ?? '',
            'insured_name' => $formattedActivity['insured_name'],
            'written_share' => $request->written_share ?? '',
            'signed_share' => $request->signed_share ?? '',
            'contact_name' => $request->contact_name ?? '',
            'type_of_bus' => $formattedActivity['type_of_bus'],
            'class_name' => $formattedActivity['class_name'],
            'opportunity_id' => $formattedActivity['opportunity_id'],
            'currency_code' => $formattedActivity['currency_code'],
            'effective_date' => $formattedActivity['effective_date'],
            'closing_date' => $formattedActivity['closing_date'],
            'stage' => $request->stage_cycle_fac ?? $request->stagecycleqt ?? '',
            'stageType' => $formattedActivity['stageType'],
        ];
    }

    /**
     * Decode schedule details from JSON or return as array
     */
    private function decodeScheduleDetails($details)
    {
        if (empty($details)) {
            return [];
        }

        if (is_string($details)) {
            $decoded = json_decode($details, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($details) ? $details : [];
    }

    /**
     * Prepare customer objects for document generation
     */
    private function prepareCustomerObjects(array $requestData)
    {
        // If customer_id is not an array, create single customer object
        if (!is_array($requestData['customer_id'])) {
            return collect([(object) $this->createCustomerObject($requestData, 0)]);
        }

        return collect($requestData['customer_id'])->map(function ($customerId, $index) use ($requestData) {
            return (object) $this->createCustomerObject($requestData, $index);
        });
    }

    /**
     * Create individual customer object
     */
    private function createCustomerObject(array $requestData, int $index)
    {
        return [
            'customer_id' => is_array($requestData['customer_id']) ? $requestData['customer_id'][$index] : $requestData['customer_id'],
            'customer_name' => $requestData['customer_name'],
            'contact_name' => is_array($requestData['contact_name']) ? ($requestData['contact_name'][$index] ?? null) : $requestData['contact_name'],
            'written_share' => is_array($requestData['written_share']) ? ($requestData['written_share'][$index] ?? null) : $requestData['written_share'],
            'signed_share' => is_array($requestData['signed_share']) ? ($requestData['signed_share'][$index] ?? null) : $requestData['signed_share'],
            'schedule_details' => $requestData['schedule_details'],
            'facschedule_details' => $requestData['facschedule_details'],
            'quote_title_intro' => $requestData['quote_title_intro'],
            'insured_name' => $requestData['insured_name'],
            'type_of_bus' => $requestData['type_of_bus'],
            'opportunity_id' => $requestData['opportunity_id'],
            'effective_date' => $requestData['effective_date'],
            'closing_date' => $requestData['closing_date'],
            'class_name' => $requestData['class_name'],
            'currency_code' => $requestData['currency_code'],
            'stageType' => $requestData['stageType'],
        ];
    }

    /**
     * Generate Word document
     */
    private function generateWordDocument(array $data)
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(10);

        $styles = $this->getDocumentStyles();

        $company = $data['company'];
        $customers = $data['customers'];
        $shares = $data['shares'];
        $unplaced = $data['unplaced'];
        $updated_written_share_total = $data['updated_written_share_total'];
        $stage = $data['stage'];

        $isSpecialCase = $this->isSpecialCase($stage, $customers[0]->stageType ?? null);

        foreach ($shares as $index => $share) {
            $section = $this->createDocumentSection($phpWord);

            $this->addDocumentHeader($section, $company, $styles);

            foreach ($customers as $customer) {
                $this->addDocumentTitle($section, $customer, $styles);
                $this->addDocumentDetails($section, $customer, $styles);
                $this->addScheduleDetails($section, $customer, $styles);

                if (!$isSpecialCase) {
                    $this->addRegularShareDetails($section, $share, $customer, $styles);
                } else {
                    $this->addSpecialCaseShareDetails(
                        $section,
                        $shares,
                        $customer,
                        $updated_written_share_total,
                        $unplaced,
                        $styles
                    );
                }

                $this->addGeneratedDate($section, $styles);

                if ($index < count($shares) - 1 && !$isSpecialCase) {
                    $section->addPageBreak();
                }
            }

            $this->addDocumentFooter($section, $styles);
        }

        $fileName = 'Reinsurance_Quote_' . time() . '.docx';
        $path = storage_path('app/public/' . $fileName);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    /**
     * Generate PDF document
     */
    private function generatePdfDocument(array $data)
    {
        $view = 'printouts.fac_coverslipquote';

        $dompdf = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setWarnings(false)
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $dompdf->stream('Quotation_Cover_Slip_' . time() . '.pdf');
    }

    /**
     * Check if this is a special case scenario
     */
    private function isSpecialCase($stage, $stageType): bool
    {
        return ($stage == 3 && $stageType == 2) || ($stage == 2 && $stageType == 1);
    }

    /**
     * Get document styling configuration
     */
    private function getDocumentStyles(): array
    {
        return [
            'header' => ['bold' => true, 'size' => 12],
            'subHeader' => ['bold' => true, 'size' => 10],
            'normal' => ['size' => 10],
            'footer' => ['size' => 8],
            'center' => ['alignment' => Jc::CENTER],
            'left' => ['alignment' => Jc::LEFT],
            'right' => ['alignment' => Jc::RIGHT],
            'table' => [
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80,
            ],
            'line' => [
                'weight' => 1,
                'width' => 500,
                'height' => 0,
                'color' => '000000',
            ],
        ];
    }

    /**
     * Create document section with margins
     */
    private function createDocumentSection(PhpWord $phpWord)
    {
        return $phpWord->addSection([
            'marginTop' => Converter::cmToTwip(2),
            'marginBottom' => Converter::cmToTwip(1),
            'marginLeft' => Converter::cmToTwip(2),
            'marginRight' => Converter::cmToTwip(2),
            'pageNumberingStart' => 1,
        ]);
    }

    /**
     * Add document header with logo and company info
     */
    private function addDocumentHeader($section, $company, array $styles)
    {
        $header = $section->addHeader();
        $headerTable = $header->addTable();
        $headerTable->addRow();

        $logoCell = $headerTable->addCell(5000);
        $infoCell = $headerTable->addCell(7000);

        $logoPath = public_path('logo.png');
        if (file_exists($logoPath) && is_readable($logoPath)) {
            $logoCell->addImage($logoPath, [
                'width' => 150,
                'alignment' => Jc::RIGHT,
            ]);
        }

        $infoCell->addText($company->company_name, ['bold' => true], $styles['left']);
        $infoCell->addText($company->postal_address, [], $styles['left']);
        $infoCell->addText('Phone: ' . $company->mobilephone, [], $styles['left']);
        $infoCell->addText('Email: ' . $company->email, [], $styles['left']);
    }

    /**
     * Add document title
     */
    private function addDocumentTitle($section, $customer, array $styles)
    {
        $title = strtoupper(implode(' - ', array_filter([
            $customer->quote_title_intro,
            $customer->class_name,
            $customer->insured_name ?? 'N/A'
        ])));

        $section->addText($title, $styles['header'], $styles['center']);
        $section->addTextBreak(1);
        $section->addLine(array_merge($styles['line'], ['alignment' => Jc::LEFT]));
    }

    /**
     * Add basic document details table
     */
    private function addDocumentDetails($section, $customer, array $styles)
    {
        $detailsTable = $section->addTable($styles['table']);

        $details = [
            'Our Reference' => '',
            'Cedant Name' => ucfirst($customer->customer_name ?? 'N/A'),
            'Insured Name' => ucfirst($customer->insured_name ?? 'N/A'),
            'Insurance Group' => ucfirst($customer->type_of_bus ?? 'N/A'),
            'Class Of Business' => ucfirst($customer->class_name ?? 'N/A'),
            'Period Of Cover' => ucfirst(($customer->effective_date ?? '') . ' To ' . ($customer->closing_date ?? '')),
        ];

        foreach ($details as $field => $value) {
            $detailsTable->addRow();
            $detailsTable->addCell(4000)->addText($field . ':', $styles['subHeader'], $styles['left']);
            $detailsTable->addCell(8000)->addText($value, $styles['normal'], $styles['left']);
        }

        $section->addTextBreak(1);
    }

    /**
     * Add schedule details section
     */
    private function addScheduleDetails($section, $customer, array $styles)
    {
        $scheduleDetails = $customer->facschedule_details ?? $customer->schedule_details ?? [];

        if (empty($scheduleDetails)) {
            return;
        }

        foreach ($scheduleDetails as $detail) {
            if (empty($detail['name']) || (!isset($detail['amount']) && !isset($detail['details']))) {
                continue;
            }

            $detailName = trim(strtolower($detail['name']));

            if ($detailName === 'policy wording') {
                $section->addText(ucfirst($detail['name']), $styles['subHeader'], $styles['left']);
                if (isset($detail['details'])) {
                    $section->addText(
                        strip_tags(html_entity_decode($detail['details'])),
                        $styles['normal'],
                        $styles['left']
                    );
                }
                $section->addTextBreak(1);
            } else {
                $this->addScheduleDetailRow($section, $detail, $customer, $styles);
            }
        }

        $section->addTextBreak(1);
    }

    /**
     * Add individual schedule detail row
     */
    private function addScheduleDetailRow($section, array $detail, $customer, array $styles)
    {
        $scheduleTable = $section->addTable($styles['table']);
        $scheduleTable->addRow();

        $detailName = trim(strtolower($detail['name']));
        $suffix = $this->getDetailSuffix($detailName);

        $scheduleTable->addCell(4000)->addText(
            ucfirst($detail['name']) . $suffix,
            $styles['subHeader'],
            $styles['left']
        );

        $amountText = $this->formatDetailAmount($detail, $detailName, $customer->currency_code);
        $detailsText = isset($detail['details']) ? strip_tags(html_entity_decode($detail['details'])) : '';

        $scheduleTable->addCell(8000)->addText(
            trim($amountText . ' ' . $detailsText),
            $styles['normal'],
            $styles['left']
        );
    }

    /**
     * Get appropriate suffix for detail name
     */
    private function getDetailSuffix(string $detailName): string
    {
        if (in_array($detailName, ['cedant commission rate', 'reinsurer commission rate'])) {
            return ' (%):';
        }

        if (in_array($detailName, self::VALID_PREMIUM_FIELDS)) {
            return ' (100%):';
        }

        return ':';
    }

    /**
     * Format detail amount with currency
     */
    private function formatDetailAmount(array $detail, string $detailName, string $currencyCode): string
    {
        if (!isset($detail['amount']) || $detail['amount'] === null) {
            return '';
        }

        $amountText = '';

        if ($detailName !== 'reinsurer commission rate') {
            $amountText = $currencyCode . ' ';
        }

        $amountText .= $detail['amount'];

        if ($detailName === 'reinsurer commission rate') {
            $amountText .= '%';
        }

        return $amountText;
    }

    /**
     * Add regular share details (non-special case)
     */
    private function addRegularShareDetails($section, array $share, $customer, array $styles)
    {
        $section->addText('Placed With:', $styles['subHeader'], $styles['left']);

        $scheduleDetails = $customer->facschedule_details ?? $customer->schedule_details ?? [];
        $displayValues = $this->calculateDisplayValues($scheduleDetails);
        $selectedNames = array_keys($displayValues);

        $shareKey = isset($share['signed_share']) && $share['signed_share'] != null ? 'signed_share' : 'written_share';
        $shareValue = floatval($share[$shareKey] ?? 0);

        $shareTable = $section->addTable($styles['table']);
        $shareTable->addRow();

        $shareTable->addCell(3000)->addText('Reinsurer', $styles['subHeader'], $styles['left']);
        $shareTable->addCell(2000)->addText(
            ucfirst(str_replace('_', ' ', $shareKey)) . ' (%)',
            $styles['subHeader'],
            $styles['center']
        );

        foreach ($selectedNames as $name) {
            $shareTable->addCell(3000)->addText(
                ucfirst($name) . " ({$customer->currency_code})",
                $styles['subHeader'],
                $styles['center']
            );
        }

        $shareTable->addRow();
        $reinsurerName = ucfirst($share['customer_name'] ?? $share['name'] ?? 'N/A');
        $shareTable->addCell(3000)->addText($reinsurerName, $styles['normal'], $styles['left']);
        $shareTable->addCell(2000)->addText($shareValue . '%', $styles['normal'], $styles['center']);

        foreach ($selectedNames as $name) {
            $calculatedAmount = ($displayValues[$name] * $shareValue) / 100;
            $shareTable->addCell(3000)->addText(
                number_format($calculatedAmount, 2),
                $styles['normal'],
                $styles['center']
            );
        }
    }

    /**
     * Add special case share details
     */
    private function addSpecialCaseShareDetails($section, array $shares, $customer, $updatedWrittenShareTotal, $unplaced, array $styles)
    {
        $section->addText('Offered Share (%):', $styles['subHeader'], $styles['left']);
        $section->addText($updatedWrittenShareTotal . '%', $styles['normal'], $styles['left']);
        $section->addText('Placed With:', $styles['subHeader'], $styles['left']);

        $scheduleDetails = $customer->facschedule_details ?? $customer->schedule_details ?? [];
        $displayValues = $this->calculateDisplayValues($scheduleDetails);
        $selectedNames = array_keys($displayValues);

        $shareTable = $section->addTable($styles['table']);
        $shareTable->addRow();

        $shareTable->addCell(3000)->addText('Reinsurer', $styles['subHeader'], $styles['left']);
        $shareTable->addCell(2000)->addText('Written Share (%)', $styles['subHeader'], $styles['center']);

        foreach ($selectedNames as $name) {
            $shareTable->addCell(3000)->addText(
                ucfirst($name) . " ({$customer->currency_code})",
                $styles['subHeader'],
                $styles['center']
            );
        }

        $totalWrittenShare = 0;
        $columnTotals = array_fill_keys($selectedNames, 0);

        foreach ($shares as $share) {
            if (!isset($share['written_share']) || $share['written_share'] == null) {
                continue;
            }

            $shareTable->addRow();
            $reinsurerName = ucfirst($share['customer_name'] ?? $share['name'] ?? 'N/A');
            $shareTable->addCell(3000)->addText($reinsurerName, $styles['normal'], $styles['left']);

            $writtenShare = floatval($share['written_share']);
            $shareTable->addCell(2000)->addText($writtenShare . '%', $styles['normal'], $styles['center']);

            $totalWrittenShare += $writtenShare;

            foreach ($selectedNames as $name) {
                $calculatedAmount = ($displayValues[$name] * $writtenShare) / 100;
                $shareTable->addCell(3000)->addText(
                    number_format($calculatedAmount, 2),
                    $styles['normal'],
                    $styles['center']
                );
                $columnTotals[$name] += $calculatedAmount;
            }
        }

        // Add total row
        $shareTable->addRow();
        $shareTable->addCell(3000)->addText('Total', $styles['subHeader'], $styles['left']);
        $shareTable->addCell(2000)->addText(
            number_format($totalWrittenShare, 2) . '%',
            $styles['subHeader'],
            $styles['center']
        );

        foreach ($selectedNames as $name) {
            $shareTable->addCell(3000)->addText(
                number_format($columnTotals[$name], 2),
                $styles['subHeader'],
                $styles['center']
            );
        }

        if ($unplaced > 0) {
            $section->addTextBreak(1);
            $unplacedTable = $section->addTable($styles['table']);
            $unplacedTable->addRow();
            $unplacedTable->addCell(4000)->addText('Unplaced Share (%)', $styles['subHeader'], $styles['left']);
            $unplacedTable->addCell(8000)->addText($unplaced . '%', $styles['normal'], $styles['left']);
        }
    }

    /**
     * Calculate display values from schedule details
     */
    private function calculateDisplayValues(array $scheduleDetails): array
    {
        $displayValues = [];

        foreach ($scheduleDetails as $detail) {
            if (!isset($detail['name']) || !isset($detail['amount'])) {
                continue;
            }

            $detailName = strtolower(trim($detail['name']));

            if ($detailName === 'reinsurer commission rate' || !in_array($detailName, self::VALID_PREMIUM_FIELDS)) {
                continue;
            }

            $amount = floatval(str_replace(',', '', $detail['amount']));
            $displayValues[$detailName] = $amount;
        }

        return $displayValues;
    }

    /**
     * Add generated date footer
     */
    private function addGeneratedDate($section, array $styles)
    {
        $section->addText(
            'Generated on behalf of Acentria International on ' . date('F j, Y'),
            $styles['footer'],
            $styles['center']
        );
    }

    /**
     * Add document footer with page numbers
     */
    private function addDocumentFooter($section, array $styles)
    {
        $footer = $section->addFooter();
        $footer->addText(
            '© ' . date('Y') . ' Acentriagroup. All rights reserved. | Page No: ',
            $styles['footer'],
            $styles['right']
        );
        $footer->addPreserveText('{PAGE}', $styles['footer'], $styles['right']);
    }
}
