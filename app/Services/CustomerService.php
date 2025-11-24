<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Bd\CustomerContact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * Customer Service
 *
 * Handles business logic for customer operations
 */
class CustomerService
{
    public function getCoversByBusinessType(array $typeOfBus): Collection
    {
        $placeholders = implode(',', array_fill(0, count($typeOfBus), '?'));

        $rawQuery = "
            SELECT DISTINCT ON (cr.cover_no)
                cr.cover_no,
                cr.cover_type,
                cr.class_code,
                cr.cover_to,
                cr.created_at,
                cr.type_of_bus,
                c.name
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
        $customerType = is_array($data['customerType'])
            ? $data['customerType']
            : explode(',', $data['customerType']);

        return Customer::create([
            'name' => $data['partnerName'],
            'customer_type' => $customerType,
            'street' => $data['street'],
            'city' => $data['city'],
            'postal_address' => $data['postalCode'],
            'country_iso' => $data['country'],
            'registration_no' => $data['incorporationNo'],
            'tax_no' => $data['taxNo'],
            'email' => $data['email'],
            'financial_rate' => $data['financialRating'],
            'agency_rate' => $data['agencyRating'],
            'website' => $data['website'] ?? null,
            'telephone' => $data['telephone'] ?? null,
            'identity_number_type' => $data['identityType'],
            'identity_number' => $data['identityNo'],
            'status' => 'A',
            'created_by' => Auth::user()->user_name ?? 'system',
            'created_at' => Carbon::now(),
        ]);
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
                'is_primary' => $contactData['isPrimary'] ?? false,
                'order' => $contactData['order'] ?? 0,
                'created_at' => Carbon::now(),
            ]);
        }
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
            'updated_by' => Auth::user()->user_name ?? 'system',
            'updated_at' => Carbon::now(),
        ];

        if (isset($data['customerType'])) {
            $updateData['customer_type'] = is_array($data['customerType'])
                ? $data['customerType']
                : explode(',', $data['customerType']);
        }

        $customer->update($updateData);

        return $customer->fresh();
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
