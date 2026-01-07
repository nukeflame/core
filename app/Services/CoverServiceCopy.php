<?php

namespace App\Services;

use App\Models\CoverRegister;
use App\Models\CoverInstallment;
use App\Models\CoverReinProp;
use App\Models\CoverReinLayer;
use App\Repositories\CoverRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class CoverServiceCopy
{
    protected $coverRepository;

    public function __construct(CoverRepository $coverRepository)
    {
        $this->coverRepository = $coverRepository;
    }

    /**
     * Get form data for cover registration
     */
    public function getFormData(string $transType, $customerId = null, $endorsementNo = null): array
    {
        $data = [
            'types_of_bus' => $this->coverRepository->getBusinessTypes(),
            'covertypes' => $this->coverRepository->getCoverTypes(),
            'branches' => $this->coverRepository->getBranches(),
            'brokers' => $this->coverRepository->getBrokers(),
            'reinsdivisions' => $this->coverRepository->getReinsuranceDivisions(),
            'paymethods' => $this->coverRepository->getPaymentMethods(),
            'currencies' => $this->coverRepository->getCurrencies(),
            'premium_pay_terms' => $this->coverRepository->getPremiumPaymentTerms(),
            'classGroups' => [], //$this->coverRepository->getClassGroups(),
            'types_of_sum_insured' => $this->coverRepository->getSumInsuredTypes(),
            'treatytypes' => $this->coverRepository->getTreatyTypes(),
            'reinsclasses' => $this->coverRepository->getReinsuranceClasses(),
            'staff' => $this->coverRepository->getStaff(),
        ];

        if ($customerId) {
            $data['customer'] = $this->coverRepository->getCustomer($customerId);
            $data['insured'] = $this->coverRepository->getInsuredByCustomer($customerId);
        }

        if (in_array($transType, ['REN', 'EDIT', 'EXT', 'CNC', 'RFN', 'NIL', 'INS'])) {
            $data['old_endt_trans'] = $this->coverRepository->getCoverByEndorsement($endorsementNo);
            $data['coverInstallments'] = $this->coverRepository->getCoverInstallments($endorsementNo);

            if (in_array($transType, ['EDIT'])) {
                $data['coverreinpropClasses'] = $this->coverRepository->getCoverReinPropClasses($endorsementNo);
                $data['coverReinLayers'] = $this->coverRepository->getCoverReinLayers($endorsementNo);
                $data['premtypes'] = $this->coverRepository->getPremiumTypes($endorsementNo);
                $data['reinPremTypes'] = $this->coverRepository->getReinsurancePremiumTypes();
            }
        }

        if ($transType === 'REN' && isset($data['old_endt_trans'])) {
            $data['renewal_date'] = $this->calculateRenewalDate($data['old_endt_trans']->cover_to);
        }

        return $data;
    }

    /**
     * Register a new cover
     */
    public function registerNewCover(array $data): CoverRegister
    {
        DB::beginTransaction();

        try {
            // Generate cover number
            $coverNo = $this->generateCoverNumber($data['type_of_bus'], $data['branchcode']);

            // Create main cover record
            $cover = $this->coverRepository->registerCover(array_merge($data, [
                'cover_no' => $coverNo,
                'endorsement_no' => $coverNo . '/000',
                'status' => 'ACTIVE',
                'created_by' => auth()->id(),
            ]));

            // Process installments if payment method is installment
            if ($this->isInstallmentPayment($data['pay_method'])) {
                $this->processInstallments($cover, $data);
            }

            // Process based on business type
            $this->processBusinessTypeSpecifics($cover, $data);

            DB::commit();

            return new CoverRegister();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Renew existing cover
     */
    public function renewCover(array $data)
    {
        $oldCover = $this->coverRepository->getCoverByNo($data['cover_no']);

        // Generate new endorsement number
        $newEndorsementNo = $this->generateEndorsementNumber($data['cover_no']);

        return $this->registerNewCover(array_merge($data, [
            'cover_no' => $data['cover_no'],
            'endorsement_no' => $newEndorsementNo,
            'previous_endorsement_no' => $oldCover->endorsement_no,
        ]));
    }

    /**
     * Process endorsement (EXT, CNC, RFN, NIL)
     */
    public function processEndorsement(array $data, string $type)
    {
        $oldCover = $this->coverRepository->getCoverByEndorsement($data['endorsement_no']);

        // Generate new endorsement number
        $newEndorsementNo = $this->generateEndorsementNumber($data['cover_no']);

        // Create endorsement record
        $endorsement = $this->coverRepository->createCover(array_merge($data, [
            'endorsement_no' => $newEndorsementNo,
            'previous_endorsement_no' => $oldCover->endorsement_no,
            'endorsement_type' => $type,
            'created_by' => auth()->id(),
        ]));

        return $endorsement;
    }

    /**
     * Update existing cover
     */
    public function updateCover(string $coverNo, array $data)
    {
        DB::beginTransaction();

        try {
            $cover = $this->coverRepository->updateCover($coverNo, $data);

            // Update installments if needed
            if ($this->isInstallmentPayment($data['pay_method'])) {
                $this->updateInstallments($cover, $data);
            }

            // Update business type specifics
            $this->updateBusinessTypeSpecifics($cover, $data);

            DB::commit();

            return $cover;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process installment payment
     */
    public function processInstallment(array $data)
    {
        // Implementation for processing installment payments
        return $this->processEndorsement($data, 'INSTALLMENT');
    }

    /**
     * Process business type specific data
     */
    protected function processBusinessTypeSpecifics(CoverRegister $cover, array $data): void
    {
        $businessType = $data['type_of_bus'];

        switch ($businessType) {
            case 'FPR': // Facultative Proportional
            case 'FNP': // Facultative Non-Proportional
                $this->processFacultativeCover($cover, $data);
                break;

            case 'TPR': // Treaty Proportional
                $this->processTreatyProportionalCover($cover, $data);
                break;

            case 'TNP': // Treaty Non-Proportional
                $this->processTreatyNonProportionalCover($cover, $data);
                break;
        }
    }

    /**
     * Process facultative cover
     */
    protected function processFacultativeCover(CoverRegister $cover, array $data): void
    {
        // Save facultative specific data
        $this->coverRepository->saveFacultativeData($cover->id, [
            'class_code' => $data['classcode'],
            'insured_name' => $data['insured_name'],
            'date_offered' => $data['fac_date_offered'],
            'sum_insured_type' => $data['sum_insured_type'],
            'total_sum_insured' => $this->parseAmount($data['total_sum_insured']),
            'apply_eml' => $data['apply_eml'],
            'eml_rate' => $data['eml_rate'] ?? null,
            'eml_amount' => $this->parseAmount($data['eml_amt'] ?? 0),
            'effective_sum_insured' => $this->parseAmount($data['effective_sum_insured']),
            'risk_details' => $data['risk_details'],
            'cedant_premium' => $this->parseAmount($data['cede_premium']),
            'rein_premium' => $this->parseAmount($data['rein_premium']),
            'share_offered' => $data['fac_share_offered'],
            'cedant_comm_rate' => $data['comm_rate'],
            'cedant_comm_amount' => $this->parseAmount($data['comm_amt']),
            'rein_comm_type' => $data['reins_comm_type'],
            'rein_comm_rate' => $data['reins_comm_rate'] ?? null,
            'rein_comm_amount' => $this->parseAmount($data['reins_comm_amt']),
            'brokerage_comm_type' => $data['brokerage_comm_type'] ?? null,
            'brokerage_comm_rate' => $data['brokerage_comm_rate'] ?? null,
            'brokerage_comm_amt' => $this->parseAmount($data['brokerage_comm_amt'] ?? 0),
        ]);
    }

    /**
     * Process treaty proportional cover
     */
    protected function processTreatyProportionalCover(CoverRegister $cover, array $data): void
    {
        // Save treaty common data
        $this->saveTreatyCommonData($cover, $data);

        // Process reinsurance classes
        if (isset($data['treaty_reinclass'])) {
            foreach ($data['treaty_reinclass'] as $index => $reinclass) {
                $this->processReinClass($cover, $data, $index);
            }
        }
    }

    /**
     * Process treaty non-proportional cover
     */
    protected function processTreatyNonProportionalCover(CoverRegister $cover, array $data): void
    {
        // Save treaty common data
        $this->saveTreatyCommonData($cover, $data);

        // Process layers
        if (isset($data['layer_no'])) {
            foreach ($data['layer_no'] as $index => $layerNo) {
                $this->processLayer($cover, $data, $index);
            }
        }
    }

    /**
     * Save treaty common data
     */
    protected function saveTreatyCommonData(CoverRegister $cover, array $data): void
    {
        $this->coverRepository->saveTreatyData($cover->id, [
            'treaty_code' => $data['treatytype'],
            'date_offered' => $data['date_offered'],
            'share_offered' => $data['share_offered'],
            'prem_tax_rate' => $data['prem_tax_rate'],
            'ri_tax_rate' => $data['ri_tax_rate'],
            'brokerage_comm_rate' => $data['brokerage_comm_rate'],
            'reinsurer_per_treaty' => $data['reinsurer_per_treaty'] ?? 'N',
            'port_prem_rate' => $data['port_prem_rate'] ?? null,
            'port_loss_rate' => $data['port_loss_rate'] ?? null,
            'profit_comm_rate' => $data['profit_comm_rate'] ?? null,
            'mgnt_exp_rate' => $data['mgnt_exp_rate'] ?? null,
            'deficit_yrs' => $data['deficit_yrs'] ?? null,
        ]);
    }

    /**
     * Process reinsurance class
     */
    protected function processReinClass(CoverRegister $cover, array $data, int $index): void
    {
        // Save quota share data
        $this->coverRepository->saveReinProp($cover->id, [
            'reinclass' => $data['treaty_reinclass'][$index],
            'item_description' => 'QUOTA',
            'treaty_limit' => $this->parseAmount($data['quota_share_total_limit'][$index] ?? 0),
            'retention_rate' => $data['retention_per'][$index] ?? 0,
            'retention_amount' => $this->parseAmount($data['quota_retention_amt'][$index] ?? 0),
            'treaty_rate' => $data['treaty_reice'][$index] ?? 0,
            'treaty_amount' => $this->parseAmount($data['quota_treaty_limit'][$index] ?? 0),
            'estimated_income' => $this->parseAmount($data['estimated_income'][$index] ?? 0),
            'cashloss_limit' => $this->parseAmount($data['cashloss_limit'][$index] ?? 0),
        ]);

        // Save surplus data if applicable
        if (isset($data['no_of_lines'][$index])) {
            $this->coverRepository->saveReinProp($cover->id, [
                'reinclass' => $data['treaty_reinclass'][$index],
                'item_description' => 'SURPLUS',
                'retention_amount' => $this->parseAmount($data['surp_retention_amt'][$index] ?? 0),
                'no_of_lines' => $data['no_of_lines'][$index],
                'treaty_amount' => $this->parseAmount($data['surp_treaty_limit'][$index] ?? 0),
                'estimated_income' => $this->parseAmount($data['estimated_income'][$index] ?? 0),
                'cashloss_limit' => $this->parseAmount($data['cashloss_limit'][$index] ?? 0),
            ]);
        }

        // Save premium type commission rates
        $this->processPremiumTypes($cover, $data, $index);
    }

    /**
     * Process premium types
     */
    protected function processPremiumTypes(CoverRegister $cover, array $data, int $classIndex): void
    {
        if (!isset($data['prem_type_code'])) {
            return;
        }

        foreach ($data['prem_type_code'] as $index => $premTypeCode) {
            // Check if this premium type belongs to this class
            if ($data['prem_type_reinclass'][$index] === $data['treaty_reinclass'][$classIndex]) {
                $this->coverRepository->savePremiumType($cover->id, [
                    'reinclass' => $data['prem_type_reinclass'][$index],
                    'treaty' => $data['prem_type_treaty'][$index],
                    'premtype_code' => $premTypeCode,
                    'comm_rate' => $data['prem_type_comm_rate'][$index],
                ]);
            }
        }
    }

    /**
     * Process layer
     */
    protected function processLayer(CoverRegister $cover, array $data, int $index): void
    {
        $this->coverRepository->saveReinLayer($cover->id, [
            'layer_no' => $data['layer_no'][$index],
            'reinclass' => $data['nonprop_reinclass'][$index],
            'limit_per_reinclass' => $data['limit_per_reinclass'][$index] ?? 'N',
            'indemnity_limit' => $this->parseAmount($data['indemnity_treaty_limit'][$index]),
            'underlying_limit' => $this->parseAmount($data['underlying_limit'][$index]),
            'egnpi' => $this->parseAmount($data['egnpi'][$index]),
            'min_bc_rate' => $data['min_bc_rate'][$index] ?? null,
            'max_bc_rate' => $data['max_bc_rate'][$index] ?? null,
            'flat_rate' => $data['flat_rate'][$index] ?? null,
            'upper_adj' => $data['upper_adj'][$index] ?? null,
            'lower_adj' => $data['lower_adj'][$index] ?? null,
            'min_deposit' => $this->parseAmount($data['min_deposit'][$index]),
            'reinstatement_type' => $data['reinstatement_type'][$index] ?? null,
            'reinstatement_value' => $data['reinstatement_value'][$index] ?? null,
        ]);
    }

    /**
     * Process installments
     */
    protected function processInstallments(CoverRegister $cover, array $data): void
    {
        if (!isset($data['installment_date']) || !is_array($data['installment_date'])) {
            return;
        }

        foreach ($data['installment_date'] as $index => $date) {
            $this->coverRepository->saveInstallment($cover->id, [
                'installment_no' => $data['installment_no'][$index],
                'installment_date' => $date,
                'installment_amt' => $this->parseAmount($data['installment_amt'][$index]),
                'status' => 'PENDING',
            ]);
        }
    }

    /**
     * Update installments
     */
    protected function updateInstallments(CoverRegister $cover, array $data): void
    {
        // Delete existing installments
        $this->coverRepository->deleteInstallments($cover->id);

        // Create new installments
        $this->processInstallments($cover, $data);
    }

    /**
     * Update business type specifics
     */
    protected function updateBusinessTypeSpecifics(CoverRegister $cover, array $data): void
    {
        // Delete existing records
        $this->coverRepository->deleteCoverSpecifics($cover->id);

        // Create new records
        $this->processBusinessTypeSpecifics($cover, $data);
    }

    /**
     * Generate cover number
     */
    protected function generateCoverNumber(string $busType, string $branchCode): string
    {
        $year = date('Y');
        $sequence = $this->coverRepository->getNextSequence($busType, $branchCode, $year);

        return sprintf('%s/%s/%s/%04d', $busType, $branchCode, $year, $sequence);
    }

    /**
     * Generate endorsement number
     */
    protected function generateEndorsementNumber(string $coverNo): string
    {
        $sequence = $this->coverRepository->getNextEndorsementSequence($coverNo);

        return sprintf('%s/%03d', $coverNo, $sequence);
    }

    /**
     * Calculate renewal date
     */
    protected function calculateRenewalDate($coverToDate): string
    {
        return date('Y-m-d', strtotime($coverToDate . ' +1 day'));
    }

    /**
     * Check if payment method is installment
     */
    protected function isInstallmentPayment($payMethodCode): bool
    {
        $payMethod = $this->coverRepository->getPaymentMethod($payMethodCode);
        return $payMethod && $payMethod->short_description === 'I';
    }

    /**
     * Parse amount removing commas
     */
    protected function parseAmount($amount): float
    {
        if (is_string($amount)) {
            return (float) str_replace(',', '', $amount);
        }
        return (float) $amount;
    }
}
