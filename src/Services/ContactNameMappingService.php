<?php

namespace Nukeflame\Core\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class ContactNameMappingService
{
    /**
     * Get comprehensive recipient name mapping from various sources
     */
    public static function getRecipientNames($customer, $allEmails): array
    {
        $emailToNameMap = [];

        try {
            $allEmails = array_filter(array_unique((array) $allEmails));

            if ($customer) {
                if (!empty($customer->contact_email)) {
                    $emailToNameMap[$customer->contact_email] = self::formatCustomerName($customer);
                }

                if (method_exists($customer, 'contacts') && $customer->contacts) {
                    foreach ($customer->contacts as $contact) {
                        if (!empty($contact->email)) {
                            $emailToNameMap[$contact->email] = self::formatContactName($contact);
                        }
                    }
                }

                if (method_exists($customer, 'representatives') && $customer->representatives) {
                    foreach ($customer->representatives as $rep) {
                        if (!empty($rep->email)) {
                            $emailToNameMap[$rep->email] = self::formatContactName($rep);
                        }
                    }
                }
            }

            if (Schema::hasTable('contacts')) {
                $contacts = DB::table('contacts')
                    ->whereIn('email', $allEmails)
                    ->select('email', 'first_name', 'last_name', 'name', 'full_name')
                    ->get();

                foreach ($contacts as $contact) {
                    if (!isset($emailToNameMap[$contact->email])) {
                        $emailToNameMap[$contact->email] = self::formatDbContactName($contact);
                    }
                }
            }

            if (Schema::hasTable('customer_contacts')) {
                $customerContacts = DB::table('customer_contacts')
                    ->whereIn('contact_email', $allEmails)
                    ->select('contact_email', 'contact_name')
                    ->get();

                foreach ($customerContacts as $contact) {
                    if (!isset($emailToNameMap[$contact->contact_email])) {
                        $emailToNameMap[$contact->contact_email] = self::formatDbContactName($contact);
                    }
                }
            }

            if (Schema::hasTable('reinsurer_contacts')) {
                $reinsurerContacts = DB::table('reinsurer_contacts')
                    ->whereIn('email', $allEmails)
                    ->select('email', 'first_name', 'last_name', 'name', 'contact_person', 'company_name')
                    ->get();

                foreach ($reinsurerContacts as $contact) {
                    if (!isset($emailToNameMap[$contact->email])) {
                        $emailToNameMap[$contact->email] = self::formatDbContactName($contact);
                    }
                }
            }

            if (Schema::hasTable('users')) {
                $users = DB::table('users')
                    ->whereIn('email', $allEmails)
                    ->select('email', 'name')
                    ->get();

                foreach ($users as $user) {
                    if (!isset($emailToNameMap[$user->email])) {
                        $emailToNameMap[$user->email] = self::formatDbContactName($user);
                    }
                }
            }

            foreach ($allEmails as $email) {
                if (!isset($emailToNameMap[$email])) {
                    $emailToNameMap[$email] = self::extractNameFromEmail($email);
                }
            }
        } catch (Exception $e) {
            foreach ($allEmails as $email) {
                if (!isset($emailToNameMap[$email])) {
                    $emailToNameMap[$email] = 'Sir/Madam';
                }
            }
        }

        return $emailToNameMap;
    }

    /**
     * Format customer name
     */
    private static function formatCustomerName($customer): string
    {
        if (is_object($customer)) {
            $firstName = $customer->first_name ?? $customer->firstname ?? null;
            $lastName = $customer->last_name ?? $customer->lastname ?? null;
            $fullName = $customer->name ?? $customer->customer_name ?? $customer->company_name ?? null;

            if ($firstName) {
                return $firstName;
            } elseif ($fullName) {
                // For companies, try to extract contact person or use company name
                if (
                    str_contains(strtolower($fullName), 'ltd') ||
                    str_contains(strtolower($fullName), 'limited') ||
                    str_contains(strtolower($fullName), 'inc') ||
                    str_contains(strtolower($fullName), 'corp')
                ) {
                    return 'Dear Team'; // More appropriate for companies
                }

                $nameParts = explode(' ', trim($fullName));
                return $nameParts[0];
            }
        }

        return 'Sir/Madam';
    }

    /**
     * Format contact name from contact object/model
     */
    private static function formatContactName($contact): string
    {
        if (is_object($contact) || is_array($contact)) {
            $data = is_array($contact) ? $contact : (array) $contact;

            $firstName = $data['first_name'] ?? $data['firstname'] ?? null;
            $lastName = $data['last_name'] ?? $data['lastname'] ?? null;
            $fullName = $data['name'] ?? $data['full_name'] ?? $data['contact_name'] ?? $data['contact_person'] ?? null;

            if ($firstName) {
                return $firstName;
            } elseif ($fullName) {
                $nameParts = explode(' ', trim($fullName));
                return $nameParts[0];
            }
        }

        return 'Sir/Madam';
    }

    /**
     * Format contact name from database record
     */
    private static function formatDbContactName($contact): string
    {
        $firstName = $contact->first_name ?? null;
        $lastName = $contact->last_name ?? null;
        $fullName = $contact->name ?? $contact->contact_name ?? $contact->contact_person ?? $contact->broker_name ?? null;

        if ($firstName) {
            return $firstName;
        } elseif ($fullName) {
            // Handle company names vs personal names
            if (
                str_contains(strtolower($fullName), 'ltd') ||
                str_contains(strtolower($fullName), 'limited') ||
                str_contains(strtolower($fullName), 'inc') ||
                str_contains(strtolower($fullName), 'corp') ||
                str_contains(strtolower($fullName), 'insurance') ||
                str_contains(strtolower($fullName), 'reinsurance')
            ) {
                return 'Dear Team';
            }

            $nameParts = explode(' ', trim($fullName));
            return ucfirst(strtolower($nameParts[0]));
        }

        return 'Sir/Madam';
    }

    /**
     * Extract name from email address
     */
    private static function extractNameFromEmail($email): string
    {
        try {
            $localPart = explode('@', $email)[0];

            // Common email prefixes that indicate generic/system emails
            $genericPrefixes = ['no-reply', 'noreply', 'info', 'admin', 'support', 'contact', 'hello', 'mail'];

            foreach ($genericPrefixes as $prefix) {
                if (str_starts_with(strtolower($localPart), $prefix)) {
                    return 'Dear Team';
                }
            }

            // Replace separators and extract potential name
            $name = str_replace(['.', '_', '-', '+', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], ' ', $localPart);
            $nameParts = array_filter(explode(' ', $name));

            if (!empty($nameParts)) {
                $firstName = ucfirst(strtolower($nameParts[0]));

                // Check if it looks like a real name (not too short or containing numbers)
                if (strlen($firstName) >= 2 && ctype_alpha($firstName)) {
                    return $firstName;
                }
            }
        } catch (Exception $e) {
        }

        return 'Sir/Madam';
    }

    /**
     * Get appropriate greeting based on recipient type
     */
    public static function getAppropriateGreeting($recipientName, $email = null): string
    {
        if ($recipientName === 'Dear Team') {
            return 'Dear Team,';
        } elseif ($recipientName === 'Sir/Madam') {
            return 'Dear Sir/Madam,';
        } else {
            return 'Dear ' . $recipientName . ',';
        }
    }

    /**
     * Validate and clean email addresses
     */
    private static function cleanEmailArray($emails): array
    {
        if (!is_array($emails)) {
            $emails = [$emails];
        }

        return array_filter(
            array_unique($emails),
            fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL)
        );
    }
}
