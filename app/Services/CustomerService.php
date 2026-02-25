<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Bd\CustomerContact;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

class CustomerService
{
    public function getCoversByBusinessType(array $typeOfBus): Collection
    {
        $placeholders = implode(',', array_fill(0, count($typeOfBus), '?'));

        $rawQuery = "
            SELECT DISTINCT ON (cr.cover_no)
                cr.cover_no,
                cr.endorsement_no,
                cr.transaction_type,
                cr.cover_type,
                cr.class_code,
                cr.cover_from,
                cr.cover_to,
                cr.status,
                cr.cancelled,
                cr.verified,
                cr.account_year,
                cr.created_at,
                cr.type_of_bus,
                c.name AS cedant_name
            FROM cover_register cr
            INNER JOIN customers c ON cr.customer_id = c.customer_id
            WHERE cr.type_of_bus IN ($placeholders)
            ORDER BY cr.cover_no, cr.created_at DESC
        ";

        $results = DB::select($rawQuery, $typeOfBus);

        return collect($results);
    }

    public function createCustomer(array $data): Customer
    {
        DB::beginTransaction();

        try {
            $customer = $this->createCustomerRecord($data);

            DB::commit();

            return $customer->load(['customerTypes', 'contacts']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createCustomerContacts(int $customerId, array $contacts): void
    {
        foreach ($contacts as $contactData) {
            if (empty($contactData['name']) && empty($contactData['email']) && empty($contactData['mobile'])) {
                continue;
            }

            CustomerContact::create([
                'customer_id' => $customerId,
                'contact_name' => $contactData['name'] ?? null,
                'contact_position' => $contactData['position'] ?? null,
                'contact_mobile_no' => $contactData['mobile'] ?? null,
                'contact_email' => $contactData['email'] ?? null,
                'department' => $contactData['department'] ?? null,
                'is_primary' => $contactData['isPrimary'] ?? false,
                'order' => $contactData['order'] ?? 0,
                'created_at' => Carbon::now(),
            ]);
        }
    }

    protected function createCustomerRecord(array $data): Customer
    {
        return Customer::create([
            'partner_name' => $data['partnerName'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],

            'incorporation_no' => $data['incorporationNo'] ?? null,
            'tax_no' => $data['taxNo'] ?? null,
            'identity_type' => $data['identityType'] ?? null,
            'identity_no' => $data['identityNo'] ?? null,
            'website' => $data['website'] ?? null,

            'security_rating' => $data['securityRating'] ?? null,
            'rating_agency' => $data['ratingAgency'] ?? null,
            'rating_date' => $data['ratingDate'] ?? null,
            'regulator_license_no' => $data['regulatorLicenseNo'] ?? null,
            'licensing_authority' => $data['licensingAuthority'] ?? null,
            'licensing_territory' => $data['licensingTerritory'] ?? null,
            'aml_details' => $data['amlDetails'] ?? null,
            'insured_type' => $data['insuredType'] ?? null,
            'industry_occupation' => $data['industryOccupation'] ?? null,
            'date_of_birth_incorporation' => $data['dateOfBirthIncorporation'] ?? null,

            'country' => $data['country'],
            'street' => $data['street'],
            'city' => $data['city'],
            'state' => $data['state'] ?? null,
            'postal_code' => $data['postalCode'],

            'financial_rating' => $data['financialRating'] ?? null,
            'agency_rating' => $data['agencyRating'] ?? null,

            'company_id' => $this->getCurrentCompanyId(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'status' => 'active',
        ]);
    }

    protected function getCurrentCompanyId(): ?int
    {
        return Auth::user()?->company_id ?? null;
    }


    public function getStatistics(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $totalCustomers = Customer::count();

        $customersThisMonth = Customer::where('created_at', '>=', $startOfMonth)->count();
        $customersLastMonth = Customer::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $totalChange = $this->calculatePercentageChange($customersThisMonth, $customersLastMonth);
        $activeCovers = DB::table('cover_register')->where('status',  'A')->count();

        $coversThisMonth = DB::table('cover_register')
            ->where('created_at', '>=', $startOfMonth)
            ->count();
        $coversLastMonth = DB::table('cover_register')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $coversChange = $this->calculatePercentageChange($coversThisMonth, $coversLastMonth);
        $uniqueTypes = DB::table('customers')
            ->join('customer_types', function ($join) {
                $join->whereRaw(
                    "customers.customer_type::jsonb @> to_jsonb(customer_types.type_id::text)::jsonb"
                );
            })
            ->distinct()
            ->count('customer_types.type_id');

        $typesBreakdown = DB::table('customers')
            ->join('customer_types', function ($join) {
                $join->whereRaw(
                    "customers.customer_type::jsonb @> to_jsonb(customer_types.type_id::text)::jsonb"
                );
            })
            ->select('customer_types.type_name as name', DB::raw('COUNT(*) as count'))
            ->groupBy('customer_types.type_id', 'customer_types.type_name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();


        $recentActivity = Customer::where(function ($query) use ($now) {
            $query->where('created_at', '>=', $now->copy()->subDays(7))
                ->orWhere('updated_at', '>=', $now->copy()->subDays(7));
        })->count();

        $lastUpdate = Customer::orderByDesc('updated_at')->value('updated_at');
        $sparklineData = $this->getSparklineData();

        return [
            'total_cedants' => $totalCustomers,
            'total_change' => $totalChange,
            'active_covers' => $activeCovers,
            'covers_change' => $coversChange,
            'unique_types' => $uniqueTypes,
            'types_breakdown' => $typesBreakdown,
            'recent_activity' => $recentActivity,
            'last_update' => $lastUpdate,
            'sparkline_data' => $sparklineData,
        ];
    }

    private function calculatePercentageChange(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function getSparklineData(): array
    {
        $days = 7;
        $totalCustomers = [];
        $activeCovers = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $nextDate = $date->copy()->addDay();

            $customerCount = Customer::where('created_at', '<', $nextDate)->count();
            $coverCount = DB::table('cover_register')
                ->where('created_at', '<', $nextDate)
                ->count();

            $totalCustomers[] = $customerCount;
            $activeCovers[] = $coverCount;
        }

        return [
            'total_customers' => $totalCustomers,
            'active_covers' => $activeCovers,
        ];
    }

    public function updateCustomer(int $customerId, array $data): Customer
    {
        return DB::transaction(function () use ($customerId, $data) {
            $customer = Customer::findOrFail($customerId);

            $updateData = [
                'name' => $data['partnerName'] ?? $customer->name,
                'street' => $data['street'] ?? $customer->street,
                'city' => $data['city'] ?? $customer->city,
                'postal_address' => $data['postalCode'] ?? $customer->postal_address,
                'country_iso' => $data['country'] ?? $customer->country_iso,
                'email' => $data['email'] ?? $customer->email,
                'financial_rate' => $data['financialRating'] ?? $customer->financial_rate,
                'agency_rate' => $data['agencyRating'] ?? $customer->agency_rate,
                'website' => $data['website'] ?? $customer->website,
                'telephone' => $data['telephone'] ?? $customer->telephone,
                'registration_no' => $data['incorporationNo'] ?? $customer->registration_no,
                'tax_no' => $data['taxNo'] ?? $customer->tax_no,
                'identity_number_type' => $data['identityType'] ?? $customer->identity_number_type,
                'identity_number' => $data['identityNo'] ?? $customer->identity_number,
                'updated_at' => Carbon::now(),
            ];

            if (isset($data['customerType'])) {
                $customerTypes = is_array($data['customerType'])
                    ? $data['customerType']
                    : explode(',', $data['customerType']);
                $updateData['customer_type'] = json_encode(array_values(array_map('strval', $customerTypes)));
            }

            $dynamicFieldMap = [
                'regulatorLicenseNo' => 'regulator_license_no',
                'licensingAuthority' => 'licensing_authority',
                'licensingTerritory' => 'licensing_territory',
                'amlDetails' => 'aml_details',
            ];

            foreach ($dynamicFieldMap as $inputKey => $columnName) {
                if (array_key_exists($inputKey, $data) && Schema::hasColumn('customers', $columnName)) {
                    $updateData[$columnName] = $data[$inputKey];
                }
            }

            if (array_key_exists('state', $data) && Schema::hasColumn('customers', 'state')) {
                $updateData['state'] = $data['state'];
            }

            $extendedDynamicFieldMap = [
                'securityRating' => 'security_rating',
                'ratingAgency' => 'rating_agency',
                'ratingDate' => 'rating_date',
                'insuredType' => 'insured_type',
                'industryOccupation' => 'industry_occupation',
                'dateOfBirthIncorporation' => 'date_of_birth_incorporation',
            ];

            foreach ($extendedDynamicFieldMap as $inputKey => $columnName) {
                if (array_key_exists($inputKey, $data) && Schema::hasColumn('customers', $columnName)) {
                    $updateData[$columnName] = $data[$inputKey];
                }
            }

            DB::table('customers')
                ->where('customer_id', $customerId)
                ->update($updateData);

            $this->upsertCustomerContacts($customerId, $data['contacts'] ?? []);

            return $customer->fresh();
        });
    }

    private function upsertCustomerContacts(int $customerId, array $contacts): void
    {

        $existingContactIds = CustomerContact::where('customer_id', $customerId)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        foreach ($contacts as $index => $contactData) {
            if (
                empty($contactData['name']) &&
                empty($contactData['email']) &&
                empty($contactData['mobile'])
            ) {
                continue;
            }

            $payload = [
                'customer_id' => $customerId,
                'contact_name' => $contactData['name'] ?? null,
                'contact_position' => $contactData['position'] ?? null,
                'contact_mobile_no' => $contactData['mobile'] ?? null,
                'contact_email' => $contactData['email'] ?? null,
                'department' => $contactData['department'] ?? null,
                'is_primary' => (bool) ($contactData['isPrimary'] ?? ($index === 0)),
                'order' => $contactData['order'] ?? $index,
            ];

            $contactId = isset($contactData['id']) ? (int) $contactData['id'] : 0;
            $canUpdate = $contactId > 0 && in_array($contactId, $existingContactIds, true);

            if ($canUpdate) {
                CustomerContact::where('id', $contactId)
                    ->where('customer_id', $customerId)
                    ->update($payload);
            } else {
                CustomerContact::create($payload);
            }
        }
    }

    public function deleteCustomer(int $customerId): bool
    {
        $customer = Customer::findOrFail($customerId);
        $hasActiveCovers = DB::table('cover_register')
            ->where('customer_id', $customerId)
            ->exists();

        if ($hasActiveCovers) {
            throw new \Exception('Cannot delete customer with active covers');
        }

        return $customer->delete();
    }

    public function getCustomerWithRelations(int $customerId): Customer
    {
        return Customer::with(['contacts', 'covers', 'accountDetails'])
            ->findOrFail($customerId);
    }
}
