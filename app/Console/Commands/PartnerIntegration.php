<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Customer;
use App\Models\CoverPremium;
use App\Models\CustomerTypes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PartnerIntegration extends Command
{
    protected $signature = 'partner:glupdate';
    protected $description = 'Push all new Partners to Finance';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            Customer::where(function ($query) {
                $query->whereNull('partner_number')
                    ->orWhereRaw('TRIM(partner_number) = \'\'');
            })->orderBy('customer_id')
                ->chunk(100, function ($customers) {
                    if (count($customers) > 0) {
                        foreach ($customers as $c) {
                            $customer = Customer::where('customer_id', $c->customer_id)->first();
                            $channel_typeRecs = CustomerTypes::whereIn('type_id', (array) $customer->customer_type)->get();
                            if ($channel_typeRecs->isNotEmpty()) {

                                foreach ($channel_typeRecs as $channel_typeRec) {
                                    $requestPayload = [
                                        'partner' => [
                                            'partner_type' => 'C', // 'I' for Individual, 'C' for Corporate
                                            'channel_type' => $channel_typeRec->code,
                                            'partner_name' => $customer->name,
                                            'telephone' => str_replace(' ', '', $customer->telephone),
                                            'identity_number_type' => $customer->identity_number_type,
                                            'identity_number' => $customer->identity_number,
                                            'incorporation_no' => $customer->registration_no,
                                            'incorporation_date' => $customer->startdate,
                                            'postal_address' => $customer->postal_address,
                                            'postal_street' => $customer->street,
                                            'postal_city' => $customer->city,
                                            'country_iso' => $customer->country_iso,
                                            'email' => $customer->email,
                                            'status' => 'A',
                                            'created_by' => $customer->created_by,
                                            'updated_by' => $customer->created_by,
                                            'created_date' => $customer->created_at,
                                            'updated_date' => $customer->created_at,
                                        ],
                                    ];

                                    // Send the API request
                                    $response = Http::withOptions([
                                        'cert' => [storage_path('certs/acfinance.local-cert.pem'), 'UzMkgwdHseFXvKjB'], // Certificate with password
                                        'ssl_key' => storage_path('certs/acfinance.local-key.pem'), // Private key
                                        // 'verify' => storage_path('certs/client-ca.pem'), // Optional: CA validation
                                    ])->withHeaders([
                                        'Reference-ID' => '4fjaZ70kuZTCgTBpCu3aGbCVBNLkja5T',
                                    ])->post(env('FINANCE_URL') . '/api/postPartner', $requestPayload);

                                    // Handle the API response
                                    if ($response->successful()) {
                                        $partner_number = $response->json('partner_number');
                                        Customer::where('customer_id', $c->customer_id)->update([
                                            'partner_number' => $partner_number,
                                            'partner_number_errors' => null,
                                        ]);

                                        $this->info("Customer ID {$customer->customer_id} processed successfully.");
                                    } else {
                                        $errors = $response->json('errors') ?? 'Unexpected error structure';
                                        $formattedErrors = is_array($errors)
                                            ? collect($errors)->map(function ($messages, $field) {
                                                $msg = is_array($messages) ? $messages : [$messages];
                                                return "$field: " . implode(', ', $msg);
                                            })->implode('; ')
                                            : $errors;

                                        Customer::where('customer_id', $c->customer_id)->update([
                                            'partner_number_errors' => $formattedErrors,
                                            'partner_number' => null,
                                        ]);

                                        $this->error("Error for Customer ID {$customer->customer_id}: $formattedErrors", [
                                            'response' => $response->json()
                                        ]);
                                    }
                                }
                            } else {
                                $this->info("No channel types found for Customer ID {$customer->customer_id}");
                            }
                        }
                    }
                });
            $this->info("No customers to process.");
        } catch (\Exception $e) {
            $this->error("Error processing customers: " . $e->getMessage());
        }
    }
}
