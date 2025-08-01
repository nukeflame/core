<?php

namespace App\Imports;

use App\Models\Bd\PipelineOpportunity;
use App\Models\Bd\Prospects;
use App\Models\BusinessType;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class PipelineImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation
{
    /**
     * @var array
     */
    protected $importErrors = [];

    /**
     * Define the heading row index
     *
     * @return int
     */
    public function headingRow(): int
    {
        return 3; // 3rd row is the header
    }

    /**
     * Map row data to model
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function model(array $row)
    {
        try {
            // Filter out numeric keys and empty values
            $rows = collect($row)
                ->filter(function ($value, $key) {
                    return !is_null($key) && $value !== '' && $value !== null;
                })
                ->toArray();

            if (empty($rows)) {
                return null;
            }

            // Each row should have its own unique opportunity ID
            $opportunityId = Prospects::generateNextCode();
            $prospectData = [
                'opportunity_id' => $opportunityId,
                'stage' => 0
            ];

            foreach ($rows as $key => $value) {
                switch ($key) {
                    case 'type_of_business':
                        if (!empty($value)) {
                            $sanitizedValue = trim(Str::upper($value));
                            $businessType = BusinessType::where('bus_type_name', $sanitizedValue)
                                ->first();

                            if ($businessType) {
                                $prospectData['type_of_bus'] = $businessType->bus_type_id;
                            } else {
                                $this->addError("Business type not found: {$sanitizedValue}");
                            }
                        }
                        break;

                    case 'cedant':
                        if (!empty($value)) {
                            $sanitizedValue = trim(Str::upper($value));
                            $customer = DB::table('customers')
                                ->where(DB::raw('UPPER(name)'), $sanitizedValue)
                                ->first(['customer_id']);

                            if ($customer) {
                                $prospectData['customer_id'] = $customer->customer_id;
                            } else {
                                $this->addError("Customer not found: {$sanitizedValue}");
                            }
                        }
                        break;

                    case 'lead_type':
                        if (!empty($value)) {
                            $sanitizedValue = trim(Str::upper($value));
                            $customerType = DB::table('customer_types')
                                ->where(DB::raw('UPPER(type_name)'), $sanitizedValue)
                                ->first(['type_name']);

                            if ($customerType) {
                                $prospectData['client_type'] = $customerType->type_name;
                            } else {
                                $this->addError("Client type not found: {$sanitizedValue}");
                            }
                        }
                        break;

                    case 'lead_name':
                        if (!empty($value)) {
                            $prospectData['lead_name'] = $value;
                        }
                        break;

                    case 'year':
                        if (!empty($value)) {
                            $year = trim($value);
                            if (is_numeric($year)) {
                                $pipelines = DB::table('pipelines')->where('year', $year)->first(['id']);
                                if ($pipelines) {
                                    $prospectData['pip_year'] = $pipelines->id;
                                } else {
                                    $this->addError("Pipeline year not found: $year");
                                    $prospectData['pip_year'] = null;
                                }
                            } else {
                                logger()->info(json_encode(['year' => $year, 'value' => $value], JSON_PRETTY_PRINT));
                                $this->addError("Invalid year format: $year");
                                $prospectData['pip_year'] = null;
                            }
                        }
                        break;

                    case 'insured_category':
                        $insured_category = $value === 'New Prospect' ? 'N' : 'O';
                        $prospectData['client_category'] = $insured_category;
                        break;

                    case 'country':
                        if (!empty($value)) {
                            $countries = DB::table('countries')
                                ->where('country_name', $value)
                                ->first(['country_iso']);
                            logger()->info(json_encode(['country' => $countries], JSON_PRETTY_PRINT));

                            if ($countries) {
                                $prospectData['country_code'] = $countries->country_iso;
                            } else {
                                $this->addError("Country not found: {$value}");
                            }
                        }
                        break;

                    case 'branch':
                        if (!empty($value)) {
                            $sanitizedValue = trim(Str::upper($value));
                            $branch = DB::table('branch')
                                ->where(DB::raw('UPPER(branch_name)'), $sanitizedValue)
                                ->first(['branch_code']);

                            if ($branch) {
                                $prospectData['branchcode'] = $branch->branch_code;
                            } else {
                                $this->addError("Branch not found: {$sanitizedValue}");
                            }
                        }
                        break;

                    case 'contact_full_name':

                        // logger()->info(json_encode(['name' => $value], JSON_PRETTY_PRINT));
                        // $prospectData['contact_name'] = $value;
                        $contact_name = array_map('trim', explode(',', $value));
                        $prospectData['contact_name'] = DB::raw("'" . json_encode($contact_name) . "'::jsonb");
                        break;

                    case 'email_address':
                        $emails = array_map('trim', explode(',', $value));
                        // $prospectData['email'] = json_encode($emails);
                        $prospectData['email'] = DB::raw("'" . json_encode($emails) . "'::jsonb");
                        break;

                    case 'mobile':
                        $mobile = array_map('trim', explode(',', $value));
                        $prospectData['phone'] = DB::raw("'" . json_encode($mobile) . "'::jsonb");
                        break;

                    case 'division':
                        if (!empty($value)) {
                            $sanitizedValue = trim(Str::upper($value));
                            $reinsDivision = DB::table('reins_division')
                                ->where(DB::raw('UPPER(division_name)'), $sanitizedValue)
                                ->first(['division_code']);

                            if ($reinsDivision) {
                                $prospectData['divisions'] = $reinsDivision->division_code;
                            } else {
                                $this->addError("Division not found: {$sanitizedValue}");
                            }
                        }
                        break;

                    case 'class_group':
                        if (!empty($value)) {
                            $sanitizedValue = trim(Str::upper($value));
                            $cg = DB::table('class_groups')
                                ->where(DB::raw('UPPER(group_name)'), $sanitizedValue)
                                ->first(['group_code']);

                            if ($cg) {
                                $prospectData['class_group'] = $cg->group_code;
                            } else {
                                $this->addError("Class group not found: {$sanitizedValue}");
                            }
                        }
                        break;

                    case 'class_name':
                        if (!empty($value)) {
                            $sanitizedValue = trim(Str::upper($value));
                            $classnames = DB::table('classes')
                                ->where(DB::raw('UPPER(class_name)'), $sanitizedValue)
                                ->first(['class_code']);

                            if ($classnames) {
                                $prospectData['classcode'] = $classnames->class_code;
                            } else {
                                $this->addError("Class name not found: $value");
                            }
                        }
                        break;

                    case 'insured_name':
                        if (!empty($value)) {
                            $prospectData['insured_name'] = $value;
                        }
                        break;

                    case 'industry':
                        if (!empty($value)) {
                            $sanitizedValue = trim(Str::upper($value));
                            $occupation = DB::table('occupation')
                                ->where(DB::raw('UPPER(name)'), $sanitizedValue)
                                ->first(['name']);

                            if ($occupation) {
                                $prospectData['industry'] = $occupation->name;
                            } else {
                                $this->addError("Industry/occupation not found: {$sanitizedValue}");
                            }
                        }
                        break;

                    case 'expected_closure_date':
                        // $prospectData['fac_date_offered'] = $value;
                        $prospectData['fac_date_offered'] = $this->transformDate('fac_date_offered', $value);
                        break;

                    case 'currency':
                        if (!empty($value)) {
                            $sanitizedValue = trim(Str::upper($value));
                            $currency = DB::table('currency')
                                ->where(DB::raw('UPPER(currency_code)'), $sanitizedValue)
                                ->first(['currency_code']);

                            if ($currency) {
                                $prospectData['currency_code'] = $currency->currency_code;
                            } else {
                                $this->addError("Currency not found: {$sanitizedValue}");
                            }
                        }
                        break;

                    case 'sum_insured_type':
                        if (!empty($value)) {
                            $sanitizedValue = trim(Str::upper($value));
                            $sum_insured = DB::table('type_of_sum_insured')
                                ->where(DB::raw('UPPER(sum_insured_name)'), $sanitizedValue)
                                ->first(['sum_insured_code']);

                            if ($sum_insured) {
                                $prospectData['sum_insured_type'] = $sum_insured->sum_insured_code;
                            } else {
                                $this->addError("Sum insured type not found: {$sanitizedValue}");
                            }
                        }
                        break;



                    case '100_sum_insured':
                        $prospectData['total_sum_insured'] = $this->parseNumeric($value);
                        $prospectData['effective_sum_insured'] = $this->parseNumeric($value);
                        break;

                    case 'eml':
                        $prospectData['eml_amt'] = $this->parseNumeric($value);
                        break;

                    case 'cedant_premium':
                        $prospectData['cede_premium'] = $this->parseNumeric($value);
                        break;

                    case 'reinsurer_premium':
                        $prospectData['rein_premium'] = $this->parseNumeric($value);
                        break;

                    case 'risk_details':
                        $prospectData['risk_details'] = $value;
                        break;

                    case 'written_share':
                        $prospectData['fac_share_offered'] = $this->parseNumeric($value);
                        break;

                    case 'cedant_comm_rate':
                        $prospectData['comm_rate'] = $this->parseNumeric($value);
                        break;

                    case 'cedant_comm_amount':
                        $prospectData['comm_amt'] = $this->parseNumeric($value);
                        break;

                    case 'reinsurance_commission':
                        $prospectData['reins_comm_rate'] = $this->parseNumeric($value);
                        $prospectData['reins_comm_type'] = 'R';
                        break;

                    case 'brokerage':
                        $prospectData['brokerage_comm_rate'] = $value;
                        $prospectData['brokerage_comm_type'] = 'R';
                        break;

                    case 'cover_start_date':
                        // $prospectData['effective_date'] = $value;
                        $prospectData['effective_date'] = $this->transformDate('cover_start_date', $value);
                        break;

                    case 'cover_end_date':
                        // $prospectData['closing_date'] = $value;
                        $prospectData['closing_date'] = $this->transformDate('cover_end_date', $value);
                        break;

                    case 'nature_of_engagement':
                        $leadsource = DB::table('leads_source')
                            ->where('name', $value)
                            ->first(['id']);

                        if ($leadsource) {
                            $prospectData['engage_type'] = $leadsource->id;
                        } else {
                            $this->addError("Nature of engagement not found: {$value}");
                        }
                        break;

                    case 'telephone':
                        $telephone = array_map('trim', explode(',', $value));
                        $prospectData['telephone'] = DB::raw("'" . json_encode($telephone) . "'::jsonb");
                        break;

                    case 'prospect_lead':
                        if (!empty($value)) {
                            $users = DB::table('users')
                                ->where('name', $value)
                                ->first(['id']);

                            if ($users) {
                                $prospectData['lead_owner'] = $users->id;
                            } else {
                                $this->addError("Nature of engagement not found: {$value}");
                            }
                        }
                        break;

                    default:
                        $prospectData[$key] = $value;
                        break;
                }
            }

            // Create and save the model
            $prospect = Prospects::updateOrCreate(
                ['opportunity_id' => $opportunityId],
                $prospectData
            );
            if (!$prospect) {
                throw new \Exception("Failed to save prospect with ID: {$opportunityId}");
            }

            // Only process a single row and then stop
            return null;
        } catch (Throwable $e) {
            $this->addError("Row processing error: {$e->getMessage()}");
            logger()->error(json_encode([
                "Pipeline import error" => [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'row' => $row
                ]
            ], JSON_PRETTY_PRINT));
            return null;
        }
    }

    /**
     * Transform date values
     *
     * @param mixed $value
     * @return \Carbon\Carbon|null
     */
    protected function transformDate($title, $value)
    {
        if (empty($value) || $value == 'null') {
            return null;
        }

        try {
            // Normalize different date formats
            if (strpos($value, '/') !== false) {
                // Convert from DD/MM/YYYY to YYYY-MM-DD
                $date = Carbon::createFromFormat('d/m/Y', $value);
            } else {
                // Handle already correct format (YYYY-MM-DD)
                $date = Carbon::parse($value);
            }

            return $date->format('Y-m-d'); // Ensure uniform format
        } catch (Throwable $e) {
            $this->addError("Invalid date format for " . Str::ucfirst(str_replace('_', ' ', $title)) . ": $value");
            return null;
        }
    }


    /**
     * Parse numeric values
     *
     * @param mixed $value
     * @return float|null
     */
    protected function parseNumeric($value)
    {
        if (empty($value)) {
            return null;
        }

        $normalized = str_replace([',', ' '], '', $value);

        if (is_numeric($normalized)) {
            return floatval($normalized);
        }

        $this->addError("Invalid numeric value: $value");
        return null;
    }

    /**
     * Add import error
     *
     * @param string $message
     * @return void
     */
    protected function addError(string $message): void
    {
        $this->importErrors[] = $message;
    }

    /**
     * Get import errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->importErrors;
    }

    /**
     * Define batch size
     *
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * Define chunk size
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Validation rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // 'type_of_business' => 'sometimes|string',
            // 'cedant' => 'sometimes|string',
            // 'lead_type' => 'sometimes|string',
            // 'amount' => 'sometimes|nullable',
            // 'premium' => 'sometimes|nullable',
            // 'sum_insured' => 'sometimes|nullable',
        ];
    }
}
