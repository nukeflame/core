<?php

namespace Nukeflame\Core\Services;

use App\Models\Prospect;
// use App\Models\Contact;
// use App\Models\ProspectInsurance;
use App\Http\Requests\ProspectRequest;
use App\Models\Bd\Prospects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProspectService
{
    /**
     * Create a new prospect with all related data
     *
     * @param ProspectRequest $request
     * @return array
     */
    public function createProspect(ProspectRequest $request): array
    {
        DB::beginTransaction();

        try {
            // Create main prospect record
            $prospect = $this->createProspectRecord($request);

            // Create contact records
            $contacts = $this->createContactRecords($prospect->id, $request);

            // Create insurance details
            $insuranceDetails = $this->createInsuranceDetails($prospect->id, $request);

            // Handle file uploads if any
            $documents = $this->handleDocumentUploads($prospect->id, $request);

            DB::commit();

            Log::info('Prospects created successfully', ['prospect_id' => $prospect->id]);

            return [
                'status' => 1,
                'message' => 'Prospects created successfully',
                'prospect' => $prospect,
                'contacts' => $contacts,
                'insurance_details' => $insuranceDetails,
                'documents' => $documents
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to create prospect', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 0,
                'message' => 'Failed to create prospect: ' . $e->getMessage(),
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Update existing prospect
     *
     * @param int $prospectId
     * @param ProspectRequest $request
     * @return array
     */
    public function updateProspect(int $prospectId, ProspectRequest $request): array
    {
        DB::beginTransaction();

        try {
            $prospect = Prospects::findOrFail($prospectId);

            // Update prospect record
            $prospect = $this->updateProspectRecord($prospect, $request);

            // Update contact records
            $contacts = $this->updateContactRecords($prospectId, $request);

            // Update insurance details
            $insuranceDetails = $this->updateInsuranceDetails($prospectId, $request);

            DB::commit();

            Log::info('Prospects updated successfully', ['prospect_id' => $prospectId]);

            return [
                'status' => 1,
                'message' => 'Prospects updated successfully',
                'prospect' => $prospect,
                'contacts' => $contacts,
                'insurance_details' => $insuranceDetails
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to update prospect', [
                'prospect_id' => $prospectId,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 0,
                'message' => 'Failed to update prospect: ' . $e->getMessage(),
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Create prospect record
     *
     * @param ProspectRequest $request
     * @return Prospect
     */
    private function createProspectRecord(ProspectRequest $request): Prospects
    {
        $prospectData = $this->prepareProspectData($request);

        return Prospects::create($prospectData);
    }

    /**
     * Update prospect record
     *
     * @param Prospects $prospect
     * @param ProspectRequest $request
     * @return Prospect
     */
    private function updateProspectRecord(Prospects $prospect, ProspectRequest $request): Prospects
    {
        $prospectData = $this->prepareProspectData($request);

        $prospect->update($prospectData);

        return $prospect->fresh();
    }

    /**
     * Prepare prospect data from request
     *
     * @param ProspectRequest $request
     * @return array
     */
    private function prepareProspectData(ProspectRequest $request): array
    {
        return [
            'type_of_bus' => $request->type_of_bus,
            'customer_id' => $request->customer_id,
            'client_type' => $request->client_type,
            'lead_name' => strtoupper($request->lead_name),
            'lead_year' => $request->lead_year,
            'client_category' => $request->client_category,
            'country_code' => $request->country_code,
            'branchcode' => $request->branchcode,
            'industry' => $request->industry,
            'engage_type' => $request->engage_type,
            'lead_owner' => $request->lead_owner,
            'effective_date' => $request->effective_date,
            'closing_date' => $request->closing_date,
            'status' => 'ACTIVE',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id()
        ];
    }

    /**
     * Create contact records
     *
     * @param int $prospectId
     * @param ProspectRequest $request
     * @return array
     */
    private function createContactRecords(int $prospectId, ProspectRequest $request): array
    {
        $contacts = [];
        $contactNames = $request->contact_name ?? [];
        $emails = $request->email ?? [];
        $phoneNumbers = $request->phone_number ?? [];
        $telephones = $request->telephone ?? [];

        for ($i = 0; $i < count($contactNames); $i++) {
            if (!empty($contactNames[$i])) {
                $contactData = [
                    'prospect_id' => $prospectId,
                    'contact_name' => strtoupper($contactNames[$i]),
                    'email' => $emails[$i] ?? '',
                    'phone_number' => $phoneNumbers[$i] ?? '',
                    'telephone' => $telephones[$i] ?? '',
                    'is_primary' => $i === 0,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id()
                ];

                $contacts[] = ''; // Contact::create($contactData);
            }
        }

        return $contacts;
    }

    /**
     * Update contact records
     *
     * @param int $prospectId
     * @param ProspectRequest $request
     * @return array
     */
    private function updateContactRecords(int $prospectId, ProspectRequest $request): array
    {
        // Delete existing contacts
        // Contact::where('prospect_id', $prospectId)->delete();

        // Create new contacts
        return $this->createContactRecords($prospectId, $request);
    }

    /**
     * Create insurance details
     *
     * @param int $prospectId
     * @param ProspectRequest $request
     * @return ProspectInsurance|null
     */
    private function createInsuranceDetails(int $prospectId, ProspectRequest $request) //: ?ProspectInsurance
    {
        if (!$this->hasInsuranceDetails($request)) {
            return null;
        }

        $insuranceData = [
            'prospect_id' => $prospectId,
            'division' => $request->division,
            'class_group' => $request->class_group,
            'classcode' => $request->classcode,
            'insured_name' => strtoupper($request->insured_name ?? ''),
            'currency_code' => $request->currency_code,
            'today_currency' => $this->parseNumericValue($request->today_currency),
            'sum_insured_type' => $request->sum_insured_type,
            'total_sum_insured' => $this->parseNumericValue($request->total_sum_insured),
            'apply_eml' => $request->apply_eml,
            'eml_rate' => $this->parseNumericValue($request->eml_rate),
            'eml_amt' => $this->parseNumericValue($request->eml_amt),
            'effective_sum_insured' => $this->parseNumericValue($request->effective_sum_insured),
            'risk_details' => $request->risk_details,
            'cede_premium' => $this->parseNumericValue($request->cede_premium),
            'rein_premium' => $this->parseNumericValue($request->rein_premium),
            'fac_share_offered' => $request->fac_share_offered,
            'comm_rate' => $this->parseNumericValue($request->comm_rate),
            'comm_amt' => $this->parseNumericValue($request->comm_amt),
            'reins_comm_type' => $request->reins_comm_type,
            'reins_comm_rate' => $this->parseNumericValue($request->reins_comm_rate),
            'reins_comm_amt' => $this->parseNumericValue($request->reins_comm_amt),
            'brokerage_comm_type' => $request->brokerage_comm_type,
            'brokerage_comm_amt' => $this->parseNumericValue($request->brokerage_comm_amt),
            'brokerage_comm_rate' => $this->parseNumericValue($request->brokerage_comm_rate),
            'fac_date_offered' => $request->fac_date_offered,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id()
        ];

        // return ProspectInsurance::create($insuranceData);
    }

    /**
     * Update insurance details
     *
     * @param int $prospectId
     * @param ProspectRequest $request
     * @return ProspectInsurance|null
     */
    private function updateInsuranceDetails(int $prospectId, ProspectRequest $request) //ProspectInsurance
    {
        // Delete existing insurance details
        // ProspectInsurance::where('prospect_id', $prospectId)->delete();

        // Create new insurance details
        return $this->createInsuranceDetails($prospectId, $request);
    }

    /**
     * Handle document uploads
     *
     * @param int $prospectId
     * @param ProspectRequest $request
     * @return array
     */
    private function handleDocumentUploads(int $prospectId, ProspectRequest $request): array
    {
        $documents = [];

        if ($request->hasFile('document_file')) {
            $documentFiles = $request->file('document_file');
            $documentNames = $request->document_name ?? [];

            foreach ($documentFiles as $index => $file) {
                if ($file && $file->isValid()) {
                    $documentName = $documentNames[$index] ?? 'Document ' . ($index + 1);
                    $fileName = $this->uploadDocument($file, $prospectId);

                    // Save document record to database
                    $documents[] = [
                        'prospect_id' => $prospectId,
                        'document_name' => $documentName,
                        'file_name' => $fileName,
                        'file_path' => storage_path('app/prospect-documents/' . $fileName),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => auth()->id(),
                        'created_at' => now()
                    ];
                }
            }
        }

        return $documents;
    }

    /**
     * Upload document file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $prospectId
     * @return string
     */
    private function uploadDocument($file, int $prospectId): string
    {
        $fileName = time() . '_' . $prospectId . '_' . $file->getClientOriginalName();
        $file->storeAs('prospect-documents', $fileName);

        return $fileName;
    }

    /**
     * Check if request has insurance details
     *
     * @param ProspectRequest $request
     * @return bool
     */
    private function hasInsuranceDetails(ProspectRequest $request): bool
    {
        return !empty($request->division) ||
            !empty($request->class_group) ||
            !empty($request->classcode);
    }

    /**
     * Parse numeric value from string (removes commas)
     *
     * @param string|null $value
     * @return float|null
     */
    private function parseNumericValue(?string $value): ?float
    {
        if (empty($value)) {
            return null;
        }

        $cleanValue = str_replace(',', '', $value);

        return is_numeric($cleanValue) ? (float) $cleanValue : null;
    }

    /**
     * Get prospect with relationships
     *
     * @param int $prospectId
     * @return Prospect|null
     */
    public function getProspectWithRelations(int $prospectId): ?Prospects
    {
        try {
            return Prospects::with([
                'contacts',
                'insuranceDetails',
                'documents',
                'customer',
                'branch',
                'leadOwner'
            ])->find($prospectId);
        } catch (Exception $e) {
            Log::error('Failed to retrieve prospect', [
                'prospect_id' => $prospectId,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Submit prospect to sales pipeline
     *
     * @param int $prospectId
     * @return array
     */
    public function submitToSalesPipeline(int $prospectId): array
    {
        DB::beginTransaction();

        try {
            $prospect = Prospects::findOrFail($prospectId);

            // Validate prospect before submission
            $validation = $this->validateProspectForSubmission($prospect);

            if (!$validation['valid']) {
                return [
                    'status' => 0,
                    'message' => 'Prospects validation failed',
                    'errors' => $validation['errors']
                ];
            }

            // Update prospect status
            $prospect->update([
                'status' => 'IN_SALES_PIPELINE',
                'submitted_to_sales_at' => now(),
                'updated_by' => auth()->id()
            ]);

            // Create pipeline entry
            $this->createPipelineEntry($prospect);

            // Send notifications
            $this->sendSalesNotifications($prospect);

            DB::commit();

            Log::info('Prospects submitted to sales pipeline', [
                'prospect_id' => $prospectId
            ]);

            return [
                'status' => 1,
                'message' => 'Prospects successfully submitted to sales pipeline'
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to submit prospect to sales pipeline', [
                'prospect_id' => $prospectId,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 0,
                'message' => 'Failed to submit to sales pipeline: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate prospect for submission to sales
     *
     * @param Prospects $prospect
     * @return array
     */
    private function validateProspectForSubmission(Prospects $prospect): array
    {
        $errors = [];

        // Check required fields
        if (empty($prospect->lead_name)) {
            $errors[] = 'Lead name is required';
        }

        if (empty($prospect->customer_id)) {
            $errors[] = 'Cedant selection is required';
        }

        if (empty($prospect->lead_owner)) {
            $errors[] = 'Prospects lead assignment is required';
        }

        // Check if prospect has at least one contact
        if ($prospect->contacts()->count() === 0) {
            $errors[] = 'At least one contact is required';
        }

        // Check insurance details for facultative business
        if (in_array($prospect->type_of_bus, ['FPR', 'FNP'])) {
            $insuranceDetails = $prospect->insuranceDetails;

            if (!$insuranceDetails) {
                $errors[] = 'Insurance details are required for facultative business';
            } else {
                if (empty($insuranceDetails->insured_name)) {
                    $errors[] = 'Insured name is required';
                }

                if (empty($insuranceDetails->total_sum_insured)) {
                    $errors[] = 'Sum insured is required';
                }

                if (empty($insuranceDetails->cede_premium)) {
                    $errors[] = 'Cedant premium is required';
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Create pipeline entry
     *
     * @param Prospects $prospect
     * @return void
     */
    private function createPipelineEntry(Prospects $prospect): void
    {
        // Implementation would depend on your pipeline structure
        // This is a placeholder for the actual pipeline creation logic
        Log::info('Pipeline entry created for prospect', [
            'prospect_id' => $prospect->id
        ]);
    }

    /**
     * Send notifications to sales team
     *
     * @param Prospects $prospect
     * @return void
     */
    private function sendSalesNotifications(Prospects $prospect): void
    {
        // Implementation would depend on your notification system
        // This is a placeholder for notification logic
        Log::info('Sales notifications sent for prospect', [
            'prospect_id' => $prospect->id
        ]);
    }

    /**
     * Search prospects by various criteria
     *
     * @param array $searchCriteria
     * @return array
     */
    public function searchProspects(array $searchCriteria): array
    {
        try {
            $query = Prospects::query();

            // Apply search filters
            if (!empty($searchCriteria['lead_name'])) {
                $query->where('lead_name', 'LIKE', '%' . $searchCriteria['lead_name'] . '%');
            }

            if (!empty($searchCriteria['customer_id'])) {
                $query->where('customer_id', $searchCriteria['customer_id']);
            }

            if (!empty($searchCriteria['status'])) {
                $query->where('status', $searchCriteria['status']);
            }

            if (!empty($searchCriteria['lead_owner'])) {
                $query->where('lead_owner', $searchCriteria['lead_owner']);
            }

            if (!empty($searchCriteria['date_from'])) {
                $query->whereDate('created_at', '>=', $searchCriteria['date_from']);
            }

            if (!empty($searchCriteria['date_to'])) {
                $query->whereDate('created_at', '<=', $searchCriteria['date_to']);
            }

            $prospects = $query->with(['customer', 'leadOwner', 'contacts'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return [
                'status' => 1,
                'data' => $prospects
            ];
        } catch (Exception $e) {
            Log::error('Prospects search failed', [
                'search_criteria' => $searchCriteria,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 0,
                'message' => 'Search failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get prospects dashboard data
     *
     * @return array
     */
    public function getDashboardData(): array
    {
        try {
            $data = [
                'total_prospects' => Prospects::count(),
                'active_prospects' => Prospects::where('status', 'ACTIVE')->count(),
                'in_pipeline' => Prospects::where('status', 'IN_SALES_PIPELINE')->count(),
                'recent_prospects' => Prospects::with(['customer', 'leadOwner'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'prospects_by_type' => Prospects::select('type_of_bus', DB::raw('count(*) as count'))
                    ->groupBy('type_of_bus')
                    ->get(),
                'prospects_by_month' => Prospects::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('count(*) as count')
                )
                    ->whereYear('created_at', date('Y'))
                    ->groupBy('month', 'year')
                    ->orderBy('month')
                    ->get()
            ];

            return [
                'status' => 1,
                'data' => $data
            ];
        } catch (Exception $e) {
            Log::error('Failed to get dashboard data', [
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 0,
                'message' => 'Failed to load dashboard data'
            ];
        }
    }
}
