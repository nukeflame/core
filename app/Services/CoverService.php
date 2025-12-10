<?php

namespace App\Services;

use App\Models\Bd\PipelineOpportunity;
use App\Models\CoverRegister;
use App\Models\CoverInstallment;
use App\Models\CoverReinProp;
use App\Models\CoverReinLayer;
use App\Models\HandoverApproval;
use App\Repositories\CoverRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class CoverService
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
            'types_of_bus' => $this->coverRepository->getCachedBusinessTypes(),
            'covertypes' => $this->coverRepository->getCachedCoverTypes(),
            'branches' => $this->coverRepository->getCachedBranches(),
            'brokers' => $this->coverRepository->getCachedBrokers(),
            'reinsdivisions' => $this->coverRepository->getCachedReinsuranceDivisions(),
            // 'paymethods' => $this->coverRepository->getCachedPaymentMethods(),
            'currencies' => $this->coverRepository->getCachedCurrencies(),
            'premium_pay_terms' => $this->coverRepository->getCachedPremiumPaymentTerms(),
            'classGroups' => $this->coverRepository->getCachedClassGroups(),
            'types_of_sum_insured' => $this->coverRepository->getCachedSumInsuredTypes(),
            'treatytypes' => $this->coverRepository->getCachedTreatyTypes(),
            'reinsclasses' => $this->coverRepository->getCachedReinsuranceClasses(),
            'staff' => $this->coverRepository->getCachedStaff(),
        ];

        if ($customerId) {
            $data['customer'] = $this->coverRepository->getCustomerById($customerId);
            $data['insured'] = $this->coverRepository->getInsuredByCustomer($customerId);
        }

        if (in_array($transType, ['REN', 'EDIT', 'EXT', 'CNC', 'RFN', 'NIL', 'INS'])) {
            $data['old_endt_trans'] = $this->coverRepository->getCoverByEndorsement($endorsementNo);
            $data['coverInstallments'] = $this->coverRepository->getCoverInstallmentsByEndorsement($endorsementNo);

            if (in_array($transType, ['EDIT'])) {
                $data['coverreinpropClasses'] = $this->coverRepository->getCoverReinPropByEndorsement($endorsementNo);
                $data['coverReinLayers'] = $this->coverRepository->getCoverReinLayersByEndorsement($endorsementNo);
                $data['premtypes'] = $this->coverRepository->getPremiumTypesByEndorsement($endorsementNo);
                $data['reinPremTypes'] = $this->coverRepository->getCachedReinsurancePremiumTypes();
            }
        }

        if ($transType === 'REN' && isset($data['old_endt_trans'])) {
            $data['renewal_date'] = $this->calculateRenewalDate($data['old_endt_trans']->cover_to);
        }

        return $data;
    }

    public function registerNewCover(array $data)
    {
        try {
            $repositoryData = $this->transformRequestData($data);

            $result = $this->coverRepository->registerCover((object) $repositoryData);

            $prospect = HandoverApproval::where(['prospect_id' => $result->prospect_id])->first();
            $prospect->intergrate = true;
            $prospect->update();

            return [
                'success' => true,
                'endorsement_no' => $result->endorsement_no ?? null,
                'customer_id' => $result->customer_id ?? null,
                'prospect_id' => $result->prospect_id ?? null,
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to register cover: ' . $e->getMessage());
        }
    }

    /**
     * Renew existing cover
     */
    public function renewCover(array $data)
    {
        DB::beginTransaction();

        try {
            $oldCover = CoverRegister::where('cover_no', $data['cover_no'])->firstOrFail();

            $repositoryData = $this->transformRequestData(array_merge($data, [
                'trans_type' => 'REN',
                'endorsement_no' => $oldCover->endorsement_no,
            ]));

            $result = $this->coverRepository->registerCover((object) $repositoryData);

            DB::commit();

            return [
                'success' => true,
                'endorsement_no' => $result->endorsement_no ?? null,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Process endorsement (EXT, CNC, RFN, NIL)
     */
    public function processEndorsement(array $data, string $type)
    {
        DB::beginTransaction();

        try {
            $repositoryData = $this->transformRequestData(array_merge($data, [
                'endorse_type' => $this->mapEndorsementType($type),
            ]));

            $result = $this->coverRepository->saveCoverEndorsement((object) $repositoryData);

            DB::commit();

            return [
                'success' => true,
                'endorsement_no' => $result['endorsement_no'] ?? null,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Update existing cover
     */
    public function updateCover(string $endorsementNo, array $data)
    {
        DB::beginTransaction();

        try {
            $repositoryData = $this->transformRequestData(array_merge($data, [
                'endorsement_no' => $endorsementNo,
            ]));

            $result = $this->coverRepository->editCoverRegister((object) $repositoryData);

            DB::commit();

            return [
                'success' => true,
                'endorsement_no' => $result->endorsement_no ?? null,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function processInstallment(array $data)
    {
        return $this->processEndorsement($data, 'INSTALLMENT');
    }

    public function transformRequestData(array $data): array
    {
        return [
            // Transaction details
            'trans_type' => $data['trans_type'] ?? 'NEW',
            'cover_no' => $data['cover_no'] ?? null,
            'endorsement_no' => $data['endorsement_no'] ?? null,

            // Basic cover info
            'type_of_bus' => $data['type_of_bus'],
            'branchcode' => $data['branchcode'],
            'brokercode' => $data['brokercode'] ?? null,
            'customer_id' => $data['customer_id'],
            'covertype' => $data['covertype'],
            'class_group' => $data['class_group'] ?? null,
            'classcode' => $data['classcode'] ?? null,
            'territorial_scope' => $data['territorial_scope'] ?? null,
            'basis_of_acceptance' => $data['basis_of_acceptance'] ?? null,

            // Cover details
            'risk_details' => $data['risk_details'] ?? null,
            'cover_from' => $data['coverfrom'] ?? null,
            'cover_to' => $data['coverto'] ?? null,
            'prem_due_date' => $data['prem_due_date'] ?? null,

            // Facultative specific
            'insured_name' => $data['insured_name'] ?? null,
            'fac_date_offered' => $data['fac_date_offered'] ?? null,
            'sum_insured_type' => $data['sum_insured_type'] ?? null,
            'total_sum_insured' => $data['total_sum_insured'] ?? 0,
            'apply_eml' => $data['apply_eml'] ?? 'N',
            'eml_rate' => $data['eml_rate'] ?? null,
            'eml_amt' => $data['eml_amt'] ?? 0,
            'effective_sum_insured' => $data['effective_sum_insured'] ?? 0,
            'cede_premium' => $data['cede_premium'] ?? 0,
            'rein_premium' => $data['rein_premium'] ?? 0,
            'fac_share_offered' => $data['fac_share_offered'] ?? 0,
            'comm_rate' => $data['comm_rate'] ?? 0,
            'comm_amt' => $data['comm_amt'] ?? 0,
            'reins_comm_type' => $data['reins_comm_type'] ?? null,
            'reins_comm_rate' => $data['reins_comm_rate'] ?? null,
            'reins_comm_amt' => $data['reins_comm_amt'] ?? 0,
            'brokerage_comm_type' => $data['brokerage_comm_type'] ?? null,
            'brokerage_comm_rate' => $data['brokerage_comm_rate'] ?? null,
            'brokerage_comm_amt' => $data['brokerage_comm_amt'] ?? 0,
            'binder_cover_no' => $data['bindercoverno'] ?? null,

            // Treaty specific
            'treatytype' => $data['treatytype'] ?? null,
            'date_offered' => $data['date_offered'] ?? null,
            'share_offered' => $data['share_offered'] ?? 0,
            'prem_tax_rate' => $data['prem_tax_rate'] ?? 0,
            'ri_tax_rate' => $data['ri_tax_rate'] ?? 0,
            'port_prem_rate' => $data['port_prem_rate'] ?? null,
            'port_loss_rate' => $data['port_loss_rate'] ?? null,
            'profit_comm_rate' => $data['profit_comm_rate'] ?? null,
            'mgnt_exp_rate' => $data['mgnt_exp_rate'] ?? null,
            'deficit_yrs' => $data['deficit_yrs'] ?? null,
            'reinsurer_per_treaty' => $data['reinsurer_per_treaty'] ?? 'N',

            // Treaty proportional arrays
            'treaty_reinclass' => $data['treaty_reinclass'] ?? null,
            'quota_share_total_limit' => $data['quota_share_total_limit'] ?? null,
            'retention_per' => $data['retention_per'] ?? null,
            'quota_retention_amt' => $data['quota_retention_amt'] ?? null,
            'treaty_reice' => $data['treaty_reice'] ?? null,
            'quota_treaty_limit' => $data['quota_treaty_limit'] ?? null,
            'estimated_income' => $data['estimated_income'] ?? null,
            'cashloss_limit' => $data['cashloss_limit'] ?? null,
            'no_of_lines' => $data['no_of_lines'] ?? null,
            'surp_retention_amt' => $data['surp_retention_amt'] ?? 0,
            'surp_treaty_limit' => $data['surp_treaty_limit'] ?? 0,
            'surp_treaty_capacity' => $data['surp_treaty_capacity'] ?? 0,
            'treaty_commission_type' => $data['treaty_commission_type'] ?? [],

            // Premium types
            'prem_type_code' => $data['prem_type_code'] ?? [],
            'prem_type_reinclass' => $data['prem_type_reinclass'] ?? [],
            'prem_type_treaty' => $data['prem_type_treaty'] ?? [],
            'prem_type_comm_rate' => $data['prem_type_comm_rate'] ?? null,
            'premium_payment_term' => $data['premium_payment_term'] ?? null,
            'premium_payment_code' => $data['premium_payment_code'] ?? null,
            'flat_prem_type_comm_rate' => $data['flat_prem_type_comm_rate'] ?? [],

            // Treaty non-proportional arrays
            'layer_no' => $data['layer_no'] ?? null,
            'nonprop_reinclass' => $data['nonprop_reinclass'] ?? null,
            'limit_per_reinclass' => $data['limit_per_reinclass'] ?? null,
            'indemnity_treaty_limit' => $data['indemnity_treaty_limit'] ?? null,
            'underlying_limit' => $data['underlying_limit'] ?? null,
            'egnpi' => $data['egnpi'] ?? null,
            'min_bc_rate' => $data['min_bc_rate'] ?? null,
            'max_bc_rate' => $data['max_bc_rate'] ?? null,
            'flat_rate' => $data['flat_rate'] ?? null,
            'upper_adj' => $data['upper_adj'] ?? null,
            'lower_adj' => $data['lower_adj'] ?? null,
            'min_deposit' => $data['min_deposit'] ?? [],
            'reinstatement_type' => $data['reinstatement_type'] ?? null,
            'reinstatement_value' => $data['reinstatement_value'] ?? null,
            'treaty_brokerage_comm_rate' => $data['treaty_brokerage_comm_rate'] ?? null,

            // Payment details
            'pay_method' => $data['pay_method'] ?? null,
            'pay_method_code' => $data['pay_method_code'] ?? null,
            'no_of_installments' => $data['no_of_installments'] ?? 0,
            'installment_no' => $data['installment_no'] ?? null,
            'installment_date' => $data['installment_date'] ?? null,
            'installment_amt' => $data['installment_amt'] ?? null,

            // Endorsement specific
            'endorse_type' => $data['endorse_type'] ?? null,
            'new_premium_due_date' => $data['new_premium_due_date'] ?? null,
            'endorse_narration' => $data['endorse_narration'] ?? null,
            'currency_code' => $data['currency_code'] ?? null,

            'today_currency' => $data['today_currency'] ?? null,
            'division' => $data['division'] ?? null,
            'vat_charged' => $data['vat_charged'] ?? null,
            'deposit_frequency' => $data['deposit_frequency'] ?? 0,

            'prospect_id' => $data['prospect_id'] ?? null,
        ];
    }

    /**
     * Map endorsement type to slug
     */
    protected function mapEndorsementType(string $type): string
    {
        return match ($type) {
            'EXTRA' => 'extension',
            'CANCEL' => 'cancellation',
            'REFUND' => 'refund',
            'NIL' => 'nil-endorsement',
            'INSTALLMENT' => 'installment',
            default => strtolower($type)
        };
    }

    /**
     * Calculate renewal date
     */
    protected function calculateRenewalDate($coverToDate): string
    {
        return date('Y-m-d', strtotime($coverToDate . ' +1 day'));
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
