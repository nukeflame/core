<?php

namespace App\Repositories;

use App\Models\Bd\PipelineOpportunity;
use App\Models\BusinessType;
use App\Models\ClaimRegister;
use App\Models\Classes;
use App\Models\ClassGroup;
use App\Models\ClauseParam;
use App\Models\CoverAttachment;
use App\Models\CoverClass;
use App\Models\CoverClause;
use App\Models\CoverDebit;
use App\Models\CoverPremium;
use App\Models\CoverPremtype;
use App\Models\CoverRegister;
use App\Models\CoverReinclass;
use App\Models\CoverReinLayer;
use App\Models\CoverRipart;
use App\Models\Customer;
use App\Models\EndorsementNarration;
use App\Models\ReinNote;
use App\Models\ScheduleHeader;
use App\Models\SystemProcess;
use App\Models\SystemProcessAction;
use App\Models\User;
use App\Models\WhtRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use App\Models\CoverInstallments;
use App\Models\CoverReinProp;
use App\Models\CoverRisk;
use App\Models\CoverSlipWording;
use App\Models\CustomerAccDet;
use App\Models\EndorsementType;
use App\Models\PayMethod;
use App\Models\PremiumPayTerm;
use App\Models\ReinclassPremtype;
use App\Models\ReinsClass;
use App\Models\SlipTemplate;
use App\Models\TreatyItemCode;
use App\Models\TreatyType;
use App\Services\SequenceService;

class CoverRepository extends BaseRepository
{
    const TYPE_FACULTATIVE_PROPORTIONAL = 'FPR';
    const TYPE_FACULTATIVE_NON_PROPORTIONAL = 'FNP';
    const TYPE_TREATY_PROPORTIONAL = 'TPR';
    const TYPE_TREATY_NON_PROPORTIONAL = 'TNP';

    const TRANSACTION_NEW = 'NEW';
    const TRANSACTION_REFUND = 'RFN';
    const TRANSACTION_CANCEL = 'CNC';

    const ENTRY_PREMIUM = 'PRM';
    const ENTRY_COMMISSION = 'COM';
    const ENTRY_CLAIM = 'CLM';
    const ENTRY_MINIMUM_DEPOSIT = 'MDP';
    const ENTRY_PREMIUM_TAX = 'PTX';
    const ENTRY_REINSURANCE_TAX = 'RTX';
    const ENTRY_BROKERAGE_COMMISSION = 'BRC';
    const ENTRY_WITHHOLDING_TAX = 'WHT';
    const ENTRY_FRONTING_FEE = 'FRF';

    const DR = 'DR';
    const CR = 'CR';

    const FLOAT_COMPARISON_EPSILON = 0.01;
    const TREATY_CODE_FAC = 'FAC';
    const TREATY_CODE_TRT = 'TRT';
    const CACHE_TTL = 3600;

    protected $fieldSearchable = [];
    private $_year;
    private $_month;
    private $_quarter;
    private $_endorsement_no;
    private $sequenceService;

    public function __construct(SequenceService $sequenceService)
    {
        parent::__construct(app());

        $now = Carbon::now();
        $this->_year = $now->year;
        $this->_month = $now->month;
        $this->_quarter = $now->quarter;

        $this->sequenceService = $sequenceService;
    }

    public function model()
    {
        return CoverRegister::class;
    }

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
            logger()->error('Failed to push criteria: ' . $e->getMessage());
        }
    }

    public function processCoverHome(Request $request)
    {
        try {
            $endorsement_no = $request->endorsement_no;

            $CoverRegister = CoverRegister::with([
                'branch:branch_code,branch_name',
                'customer:customer_id,name',
            ])->where('endorsement_no', $endorsement_no)->first();

            if (!$CoverRegister) {
                throw new \Exception('Cover register not found for endorsement: ' . $endorsement_no);
            }

            [
                $coverpart,
                $coverReinclass,
                $coverTreaties,
                $treatyClasses,
                $clauses,
                $selected_clauses,
                $endorsementNarration
            ] = $this->fetchCoverRelatedData(
                $endorsement_no,
                $CoverRegister->class_code
            );

            $ins_classes = $this->getCachedInsuranceClasses();
            $type_of_bus = $this->getCachedBusinessType($CoverRegister->type_of_bus);
            $cusType = $this->getCustomerTypes($CoverRegister->type_of_bus);
            $reinsurers = $this->getReinsurers($cusType);
            $schedHeaders = $this->getScheduleHeadersForCover($CoverRegister);
            $verifiers = $this->getVerifiers();
            $process = $this->getCachedSystemProcess('cover_registration');
            $verifyprocessAction = $this->getCachedSystemProcessAction('verify_cover');

            $debitsCount = CoverDebit::where('endorsement_no', $endorsement_no)
                ->where('reversed', 'N')
                ->count();

            $actionable = $this->isCoverActionable($endorsement_no);
            $premiumTotals = $this->calculatePremiumTotals($endorsement_no);
            $installmentDetails = $this->calculateInstallmentDetails(
                $CoverRegister,
                $debitsCount,
                $premiumTotals
            );
            $reinLayerDetails = $this->getReinLayerDetails($CoverRegister);

            $whtRates = $this->getCachedWhtRates();
            $premiumPayTerms = $this->getCachedPaymentTerms();
            $paymethods = $this->getCachedPaymentMethods();
            $selected_pay_method = $paymethods->firstWhere(
                'pay_method_code',
                $CoverRegister->pay_method_code
            );
            $isInstallment = $selected_pay_method && $selected_pay_method->short_description === 'I';

            $CoverInstallments = CoverInstallments::where([
                'cover_no' => $CoverRegister->cover_no,
                'endorsement_no' => $CoverRegister->endorsement_no,
                'dr_cr' => self::DR
            ])->get();

            $itemCodes = $this->getItemCodes();
            $classGroups = $this->getClassGroups($endorsement_no);
            $businessClasses = $this->getBusinessClasses($endorsement_no);
            $taxRates = $this->getTaxRates();

            $coverreinprop = $this->getCoverReinpProps($CoverRegister);

            return [
                'coverNo' => $CoverRegister->cover_no,
                'coverReg' => $CoverRegister,
                'coverpart' => $coverpart,
                'branch' => $CoverRegister->branch,
                'broker' => $CoverRegister->broker,
                'class' => $CoverRegister->class,
                'clauses' => $clauses,
                'selected_clauses' => $selected_clauses,
                'type_of_bus' => $type_of_bus,
                'ins_classes' => $ins_classes,
                'customer' => $CoverRegister->customer,
                'covertype' => $CoverRegister->coverType,
                'reinsurers' => $reinsurers,
                'verifiers' => $verifiers,
                'process' => $process,
                'verifyprocessAction' => $verifyprocessAction,
                'remInstallment' => $installmentDetails['remaining'],
                'nextInstallment' => $installmentDetails['next'],
                'installmentAmount' => $installmentDetails['amount'],
                'coverReinclass' => $coverReinclass,
                'actionable' => $actionable,
                'TPRTotalPrem' => $premiumTotals['premium'],
                'TPRTotalCom' => $premiumTotals['commission'],
                'TPRTotalClaim' => $premiumTotals['claim'],
                'TNPTotalMdp' => $premiumTotals['mdp'],
                'TPRTotalPremTax' => $premiumTotals['premiumTax'],
                'TPRTotalRiTax' => $premiumTotals['riTax'],
                'schedHeaders' => $schedHeaders,
                'debitsCount' => $debitsCount,
                'coverTreaties' => $coverTreaties,
                'mdpAmount' => $reinLayerDetails['mdpAmount'],
                'reinLayersCount' => $reinLayerDetails['count'],
                'reinLayers' => $reinLayerDetails['layers'],
                'whtRates' => $whtRates,
                'endorsementNarration' => $endorsementNarration,
                'paymethods' => $paymethods,
                'isInstallment' => $isInstallment,
                'coverInstallments' => $CoverInstallments,
                'premiumPayTerms' => $premiumPayTerms,
                'itemCodes' => $itemCodes,
                'classGroups' => $classGroups,
                'businessClasses' => $businessClasses,
                'taxRates' => $taxRates,
                'coverreinprop' => $coverreinprop,
                'treatyClasses' => $treatyClasses
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function getItemCodes(): array
    {
        return Cache::remember('treaty_item_codes', 3600, function () {
            return TreatyItemCode::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['item_code', 'description', 'item_type'])
                ->mapWithKeys(fn($item) => [
                    $item->item_code => [
                        'description' => $item->description,
                        'type' => $item->item_type,
                    ]
                ])
                ->toArray();
        });
    }

    protected function getClassGroups($endorsement_no): array
    {
        $coverReinclass = CoverReinclass::where('endorsement_no', $endorsement_no)->get();
        $data = [];

        foreach ($coverReinclass as $rein) {
            $reinclass = DB::table('reinsclasses')
                ->where('class_code', $rein->reinclass)
                ->first();

            if ($reinclass) {
                $data[] = [
                    'group_name' => $reinclass->class_name,
                    'group_code' => $reinclass->class_code,
                ];
            }
        }

        return $data;
    }

    protected function getBusinessClasses($endorsement_no): array
    {
        $premtypes = CoverPremtype::where('endorsement_no', $endorsement_no)->get();
        $data = [];

        foreach ($premtypes as $prem) {

            $class = ReinclassPremtype::where('premtype_code', $prem->premtype_code)
                ->with('classGroup')
                ->first();

            if (!$class || !$class->classGroup) {
                continue;
            }

            $groupCode = $class->classGroup->class_code;
            $code = $class->premtype_code;
            $name = $class->premtype_name;

            $data[$groupCode][$code] = $name;
        }

        return $data;
    }


    protected function getTaxRates(): array
    {
        return \App\Models\TaxRate::getAllCurrentRates();
    }

    public function isCoverActionable($endorsement)
    {
        $cover = CoverRegister::select('endorsement_no', 'cover_no', 'commited', 'verified', 'type_of_bus')
            ->where('endorsement_no', $endorsement)
            ->first();

        if (!$cover) {
            return false;
        }

        if ($this->isFacultativeBusiness($cover->type_of_bus)) {
            $debitted = CoverDebit::where('endorsement_no', $endorsement)->exists();

            return !($cover->commited === 'Y' || $debitted);
        } else {
            return !($cover->commited === 'Y');
        }
    }

    public function editReinsurer(Request $request)
    {
        $validated = $request->validate([
            'tran_no' => 'required|integer',
            'endorsement_no' => 'required|string',
            'reinsurer' => 'required|integer',
            'share' => 'required|numeric|min:0|max:100',
            'written_share' => 'nullable|numeric',
            'compulsory_acceptance' => 'nullable|numeric|min:0|max:100',
            'optional_acceptance' => 'nullable|numeric|min:0|max:100',
            'net_of_tax' => 'nullable|integer',
            'net_of_claims' => 'nullable|integer',
            'net_of_commission' => 'nullable|integer',
            'net_of_premium' => 'nullable|integer',
            'premium_tax' => 'nullable|integer',
            'net_withholding_tax' => 'nullable|integer',
            'comm_rate' => Rule::requiredIf(function () use ($request) {
                $cover = CoverRegister::select('type_of_bus')
                    ->where('endorsement_no', $request->endorsement_no)
                    ->first();
                return $cover && $this->isFacultativeBusiness($cover->type_of_bus);
            }),
        ]);

        DB::beginTransaction();
        try {
            $CoverRegister = CoverRegister::where('endorsement_no', $request->endorsement_no)
                ->firstOrFail();

            $coverRipart = CoverRipart::where('tran_no', $request->tran_no)
                ->where('endorsement_no', $request->endorsement_no)
                ->where('partner_no', $request->reinsurer)
                ->firstOrFail();

            $duplicatePartner = CoverRipart::where('endorsement_no', $request->endorsement_no)
                ->where('partner_no', $request->reinsurer)
                ->where('tran_no', '!=', $request->tran_no)
                ->exists();

            if ($duplicatePartner) {
                throw ValidationException::withMessages([
                    'reinsurer' => ['Reinsurer must be unique for this cover.'],
                ]);
            }

            $amounts = $this->calculateReinsurerAmounts($CoverRegister, $coverRipart, $request);
            $isTreaty = $this->isTreatyBusiness($CoverRegister->type_of_bus);
            $finalShare = $isTreaty
                ? $this->parseNumeric($request->written_share)
                : $this->parseNumeric($request->share);

            $checkboxValues = [
                'net_of_tax' => $request->net_of_tax ?? 0,
                'net_of_claims' => $request->net_of_claims ?? 0,
                'net_of_commission' => $request->net_of_commission ?? 0,
                'net_of_premium' => $request->net_of_premium ?? 0,
                'premium_tax' => $request->premium_tax ?? 0,
                'net_withholding_tax' => $request->net_withholding_tax ?? 0,
            ];

            // Checkbox calculation basis is shared across reinsurers in this endorsement.
            CoverRipart::where('endorsement_no', $request->endorsement_no)->update(array_merge(
                $checkboxValues,
                [
                    'commission_mode' => ($request->net_of_tax ?? 0) ? 'net' : 'gross',
                    'updated_by' => Auth::user()->user_name,
                    'updated_at' => now(),
                ]
            ));

            $coverRipart->update([
                'share' => $finalShare,
                'written_lines' => $request->written_share,
                'compulsory_acceptance' => $request->compulsory_acceptance,
                'optional_acceptance' => $request->optional_acceptance,
                'total_acceptance' => $finalShare,
                'commission_mode' => ($request->net_of_tax ?? 0) ? 'net' : 'gross',
                'total_sum_insured' => $amounts['total_sum_insured'],
                'sum_insured' => $amounts['sum_insured'],
                'total_premium' => $amounts['total_premium'],
                'premium' => $amounts['premium'],
                'total_commission' => $amounts['total_commission'],
                'comm_rate' => $amounts['comm_rate'],
                'commission' => $amounts['commission'],
                'wht_rate' => $amounts['wht_rate'],
                'wht_amt' => $amounts['wht_amt'],
                'fronting_rate' => $amounts['fronting_rate'],
                'fronting_amt' => $amounts['fronting_amt'],
                'brokerage_comm_amt' => $amounts['brokerage_comm_amt'],
                'net_amount' => $amounts['net_amount'],
                'updated_by' => Auth::user()->user_name,
                'updated_at' => now()
            ]);

            // Avoid refresh() on composite-key models (coverripart) because
            // Eloquent's default refresh key resolution expects scalar keys.
            $coverRipart = CoverRipart::where('tran_no', $request->tran_no)
                ->where('endorsement_no', $request->endorsement_no)
                ->where('partner_no', $request->reinsurer)
                ->firstOrFail();

            $this->createReinNotes($CoverRegister, $coverRipart, $amounts);

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_OK,
                'success' => true,
                'message' => 'Reinsurer updated successfully',
                'data' => $coverRipart
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'success' => false,
                'message' => 'Failed to update reinsurer: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function preCoverVerification(Request $request)
    {
        try {
            $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();

            if (!$cover) {
                return ['Cover not found'];
            }

            $share_offered = (float) $cover->share_offered;
            $pending = [];

            switch ($cover->type_of_bus) {
                case self::TYPE_FACULTATIVE_PROPORTIONAL:
                case self::TYPE_FACULTATIVE_NON_PROPORTIONAL:
                    $total_share_placed = (float) CoverRipart::where('endorsement_no', $request->endorsement_no)
                        ->sum('share');

                    if (abs($share_offered - $total_share_placed) > self::FLOAT_COMPARISON_EPSILON) {
                        $pending[] = 'There is a pending Reinsurer share not yet placed';
                    }
                    break;

                case self::TYPE_TREATY_PROPORTIONAL:
                case self::TYPE_TREATY_NON_PROPORTIONAL:
                    $coverTreaties = CoverPremtype::join('treaty_types', 'cover_premtypes.treaty', '=', 'treaty_types.treaty_code')
                        ->where('cover_premtypes.endorsement_no', $request->endorsement_no)
                        ->distinct('cover_premtypes.treaty')
                        ->get(['cover_premtypes.treaty', 'treaty_types.treaty_name']);

                    if ($coverTreaties->count() > 1) {
                        $totalSharePlaced = CoverRipart::select('endorsement_no', 'treaty', DB::raw('SUM(amount) as total_amount'))
                            ->where('endorsement_no', $request->endorsement_no)
                            ->groupBy('endorsement_no', 'treaty')
                            ->get();

                        $hasPendingShare = $totalSharePlaced->contains(function ($transaction) use ($share_offered) {
                            return abs($transaction->total_amount - $share_offered) > self::FLOAT_COMPARISON_EPSILON;
                        });

                        if ($hasPendingShare) {
                            $pending[] = 'There is a pending Reinsurer share not yet placed';
                        }
                    } else {
                        $total_share_placed = (float) CoverRipart::where('endorsement_no', $request->endorsement_no)
                            ->sum('share');

                        if (abs($share_offered - $total_share_placed) > self::FLOAT_COMPARISON_EPSILON) {
                            $pending[] = 'There is a pending Reinsurer share not yet placed';
                        }
                    }
                    break;
            }

            $installmentPrems = CoverInstallments::where([
                'endorsement_no' => $request->endorsement_no,
                'dr_cr' => self::DR
            ])->sum('installment_amt');

            $premiumTotals = $this->calculatePremiumTotals($request->endorsement_no);
            $installmentAmount = $premiumTotals['totalDr'] - $premiumTotals['totalCr'];

            if (abs($installmentPrems - $installmentAmount) > self::FLOAT_COMPARISON_EPSILON) {
                $pending[] = 'The total installment amount does not match the total amount.';
            }

            return $pending;
        } catch (\Exception $e) {
            return ['An internal error occurred'];
        }
    }

    public function registerCover($data)
    {
        DB::beginTransaction();
        try {
            $type_of_bus = $data->type_of_bus;
            $customer_id = $data->customer_id;

            $customer = Customer::findOrFail($customer_id);
            $treatytype = $data->treatytype ? TreatyType::where('treaty_code', $data->treatytype)->first() : null;

            $businessData = $this->prepareBusinessTypeData(
                $data,
                $type_of_bus,
                $customer,
                $treatytype
            );

            if ($data->trans_type === 'NEW') {
                $endorsementData = $this->sequenceService->generateEndorsementNumber($data->trans_type);
                $endorsement_no = $endorsementData->endorsement_no;
                $cover_serial_no = $endorsementData->serial_no;

                $coverData = $this->sequenceService->generateCoverNumber();
                $cover_no = $coverData->cover_no;

                $orig_endorsement_no = $endorsement_no;
                $CoverRegister = new CoverRegister();
            } else {
                $endorsementData = $this->sequenceService->generateEndorsementNumber($data->trans_type);
                $endorsement_no = $endorsementData->endorsement_no;
                $cover_serial_no = $endorsementData->serial_no;

                $cover_no = $data->cover_no;
                $old_endorsement_no = $data->endorsement_no;

                $prevCoverRegister = CoverRegister::where('endorsement_no', $old_endorsement_no)
                    ->firstOrFail();
                $orig_endorsement_no = $prevCoverRegister->orig_endorsement_no;

                $CoverRegister = new CoverRegister($prevCoverRegister->getAttributes());
            }

            $this->_endorsement_no = $endorsement_no;

            $this->populateCoverRegister(
                $CoverRegister,
                $data,
                $cover_no,
                $endorsement_no,
                $orig_endorsement_no,
                $cover_serial_no,
                $businessData
            );

            $CoverRegister->save();

            $this->createBusinessTypeRecords($data, $type_of_bus, $cover_no, $endorsement_no, $CoverRegister);

            if ($this->isTreatyBusiness($type_of_bus)) {
                $this->createSlipWording($cover_no, $endorsement_no, $type_of_bus);
            }

            if ((int) $data->no_of_installments > 0) {
                $this->createCoverInstallments($CoverRegister, $data);
            }

            if ($data->trans_type !== self::TRANSACTION_NEW) {
                $this->replicateFromPrevious($old_endorsement_no);
            }

            DB::commit();

            return (object) [
                'endorsement_no' => $endorsement_no,
                'customer_id' => $customer->customer_id,
                'prospect_id' => $CoverRegister->prospect_id,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function replicateFromPrevious($prev_endorsement_no, $request = null)
    {
        $cover = CoverRegister::where('endorsement_no', $this->_endorsement_no)->firstOrFail();
        $base_cover = CoverRegister::where('endorsement_no', $cover->orig_endorsement_no)->firstOrFail();
        $isChangeDueDate = $request?->endorse_type === 'change-due-date' && isset($request->new_premium_due_date);

        $this->replicateReinClasses($prev_endorsement_no);
        $this->replicateUwClasses($prev_endorsement_no);
        $this->replicateRisks($prev_endorsement_no);
        $this->replicateAttachments($prev_endorsement_no);
        $this->replicateReinsurers($prev_endorsement_no, $cover, $base_cover, $request, $isChangeDueDate);
        $this->replicatePremTypes($prev_endorsement_no);
        $this->replicateTreatyProps($prev_endorsement_no);
        $this->replicateTreatyNonPropLayers($prev_endorsement_no);
        $this->replicateInstallments($prev_endorsement_no, $cover, $isChangeDueDate, $request);
        $this->replicateClauses($prev_endorsement_no);
    }

    public function editCoverRegister($request)
    {
        DB::beginTransaction();
        try {
            $endorsement_no = $request->endorsement_no;
            $this->_endorsement_no = $endorsement_no;

            $CoverRegister = CoverRegister::where('endorsement_no', $endorsement_no)
                ->firstOrFail();

            $treatytype = $request->treatytype
                ? TreatyType::where('treaty_code', $request->treatytype)->first()
                : null;

            $businessData = $this->prepareBusinessTypeDataForEdit(
                $request,
                $CoverRegister->type_of_bus,
                $treatytype
            );

            $this->updateCoverRegister($CoverRegister, $request, $businessData);
            $CoverRegister->save();

            $this->updateBusinessTypeRecords($request, $CoverRegister->type_of_bus, $treatytype);

            $this->updateCoverInstallments($CoverRegister, $request);

            DB::commit();

            return (object) ['endorsement_no' => $CoverRegister->endorsement_no];
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    private function prepareBusinessTypeDataForEdit($request, $type_of_bus, $treatytype)
    {
        $customer = Customer::find($request->customer_id);

        if ($this->isFacultativeBusiness($type_of_bus)) {
            return $this->prepareFacultativeData($request);
        }

        if ($type_of_bus === self::TYPE_TREATY_NON_PROPORTIONAL) {
            return $this->prepareTreatyNonPropData($request, $customer, $treatytype);
        }

        if ($type_of_bus === self::TYPE_TREATY_PROPORTIONAL) {
            return $this->prepareTreatyPropData($request, $customer, $treatytype);
        }

        throw new \Exception('Invalid business type');
    }

    private function updateCoverRegister($CoverRegister, $request, array $businessData)
    {
        $ppw = null;
        if ($request->premium_payment_term) {
            $ppw = PremiumPayTerm::where('pay_term_code', $request->premium_payment_term)->first();
        }

        $CoverRegister->premium_payment_code = $request->premium_payment_term;
        $CoverRegister->premium_payment_days = $ppw ? $ppw->premium_payment_days : 0;
        $CoverRegister->branch_code = str_pad((int) $request->branchcode, 3, '0', STR_PAD_LEFT);
        $CoverRegister->broker_code = $request->brokercode ?: 0;
        $CoverRegister->cover_type = $request->covertype;
        $CoverRegister->class_code = $businessData['classcode'];
        $CoverRegister->class_group_code = $request->class_group;
        $CoverRegister->insured_name = $businessData['insured_name'] ?? $CoverRegister->insured_name;
        $CoverRegister->effective_date = $request->effective_date ?? $request->cover_from;
        $CoverRegister->cover_from = $request->cover_from;
        $CoverRegister->cover_to = $request->cover_to;
        $CoverRegister->binder_cov_no = $request->binder_cover_no;
        $CoverRegister->pay_method_code = $request->pay_method;
        $CoverRegister->currency_code = $request->currency_code;
        $CoverRegister->currency_rate = $request->today_currency;
        $CoverRegister->type_of_sum_insured = $request->sum_insured_type;
        $CoverRegister->rein_premium = $businessData['rein_premium'] ?? 0;
        $CoverRegister->total_sum_insured = $this->parseNumeric($request->total_sum_insured);
        $CoverRegister->cedant_premium = $this->parseNumeric($request->cede_premium);
        $CoverRegister->eml_rate = $this->parseNumeric($request->eml_rate);
        $CoverRegister->apply_eml = $request->apply_eml ?? 'N';
        $CoverRegister->eml_amount = $this->parseNumeric($request->eml_amt);
        $CoverRegister->effective_sum_insured = $this->parseNumeric($request->effective_sum_insured);
        $CoverRegister->cedant_comm_rate = $this->parseNumeric($request->comm_rate);
        $CoverRegister->cedant_comm_amount = $this->parseNumeric($request->comm_amt);
        $CoverRegister->rein_comm_type = $request->reins_comm_type;
        $CoverRegister->rein_comm_rate = $this->parseNumeric($request->reins_comm_rate);
        $CoverRegister->brokerage_comm_type = $request->brokerage_comm_type;
        $CoverRegister->brokerage_comm_rate = $businessData['brokerage_comm_rate'] ?? 0;
        $CoverRegister->brokerage_comm_amt = $businessData['brokerage_comm_amt'] ?? 0;
        $CoverRegister->reinsurer_per_treaty = $request->reinsurer_per_treaty;
        $CoverRegister->rein_comm_amount = $this->parseNumeric($request->reins_comm_amt);
        $CoverRegister->division_code = $request->division;
        $CoverRegister->vat_charged = $request->vat_charged;
        $CoverRegister->treaty_type = $request->treatytype;
        $CoverRegister->cover_title = $businessData['treaty_name'];
        $CoverRegister->date_offered = $businessData['date_offered'] ?? null;
        $CoverRegister->share_offered = (float) ($businessData['share_offered'] ?? 0);
        $CoverRegister->port_prem_rate = $this->parseNumeric($request->port_prem_rate);
        $CoverRegister->port_loss_rate = $this->parseNumeric($request->port_loss_rate);
        $CoverRegister->profit_comm_rate = $this->parseNumeric($request->profit_comm_rate);
        $CoverRegister->mgnt_exp_rate = $this->parseNumeric($request->mgnt_exp_rate);
        $CoverRegister->deficit_yrs = (int) $this->parseNumeric($request->deficit_yrs);
        $CoverRegister->deposit_frequency = $request->deposit_frequency ?: 0;
        $CoverRegister->prem_tax_rate = $this->parseNumeric($request->prem_tax_rate);
        $CoverRegister->ri_tax_rate = $this->parseNumeric($request->ri_tax_rate);
        $CoverRegister->risk_details = $request->risk_details;
        $CoverRegister->status = 'A';
        $CoverRegister->no_of_installments = (int) $request->no_of_installments;
        $CoverRegister->basis_of_acceptance = $request->basis_of_acceptance;
        $CoverRegister->updated_by = Auth::user()->user_name;
        $CoverRegister->updated_at = now();
    }

    private function updateBusinessTypeRecords($request, $type_of_bus, $treatytype)
    {
        $cover_no = $request->cover_no;
        $endorsement_no = $this->_endorsement_no;

        $CoverRegister = CoverRegister::where('endorsement_no', $endorsement_no)->firstOrFail();

        if ($type_of_bus === self::TYPE_TREATY_NON_PROPORTIONAL) {
            CoverReinLayer::where('endorsement_no', $endorsement_no)->delete();
            CoverReinclass::where('endorsement_no', $endorsement_no)->delete();
            $this->createTNPRecords($request, $cover_no, $endorsement_no);
            return;
        }

        if ($type_of_bus === self::TYPE_TREATY_PROPORTIONAL) {
            CoverReinProp::where('endorsement_no', $endorsement_no)->delete();
            CoverPremtype::where('endorsement_no', $endorsement_no)->delete();
            CoverReinclass::where('endorsement_no', $endorsement_no)->delete();
            $this->createTPRRecords($request, $cover_no, $endorsement_no);
            return;
        }

        if ($this->isFacultativeBusiness($type_of_bus)) {
            CoverPremium::where('endorsement_no', $endorsement_no)
                ->where('treaty', self::TREATY_CODE_FAC)
                ->whereIn('entry_type_descr', [self::ENTRY_PREMIUM, self::ENTRY_COMMISSION])
                ->delete();

            $this->createFacultativeRecords($request, $cover_no, $endorsement_no, $CoverRegister);
        }
    }

    private function updateCoverInstallments($CoverRegister, $request)
    {
        $payMethod = PayMethod::where('pay_method_code', $request->pay_method)->first();
        $isInstallment = $payMethod && $payMethod->short_description === 'I';

        CoverInstallments::where('endorsement_no', $CoverRegister->endorsement_no)
            ->where('dr_cr', self::DR)
            ->delete();

        if ($isInstallment && (int) $request->no_of_installments > 1 && is_array($request->installment_no)) {
            for ($i = 0; $i < (int) $request->no_of_installments; $i++) {
                CoverInstallments::create([
                    'cover_no' => $CoverRegister->cover_no,
                    'endorsement_no' => $CoverRegister->endorsement_no,
                    'layer_no' => 0,
                    'trans_type' => $CoverRegister->type_of_bus,
                    'entry_type' => $CoverRegister->transaction_type,
                    'installment_no' => $request->installment_no[$i],
                    'installment_date' => $request->installment_date[$i],
                    'installment_amt' => $this->parseNumeric($request->installment_amt[$i] ?? 0),
                    'dr_cr' => self::DR,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            return;
        }

        $premiumTotals = $this->calculatePremiumTotals($CoverRegister->endorsement_no);
        $installmentAmount = $premiumTotals['totalDr'] - $premiumTotals['totalCr'];

        CoverInstallments::create([
            'cover_no' => $CoverRegister->cover_no,
            'endorsement_no' => $CoverRegister->endorsement_no,
            'layer_no' => 0,
            'trans_type' => $CoverRegister->type_of_bus,
            'entry_type' => $CoverRegister->transaction_type,
            'installment_no' => 1,
            'installment_date' => $CoverRegister->cover_from->addDays((int) $CoverRegister->premium_payment_days),
            'installment_amt' => $installmentAmount,
            'dr_cr' => self::DR,
            'created_by' => Auth::user()->user_name,
            'updated_by' => Auth::user()->user_name,
        ]);
    }

    public function saveCoverEndorsement($request)
    {
        DB::beginTransaction();
        try {
            $type_of_bus = $request->type_of_bus;
            $endorse_type_slug = $request->endorse_type;

            $endorse_type = EndorsementType::where('type_of_bus', $type_of_bus)
                ->where('endorse_type_slug', $endorse_type_slug)
                ->firstOrFail();

            $trans_type = $endorse_type->transaction_type;

            $endorsementData = $this->calculateEndorsementChanges($request);

            $endorsementData = $this->sequenceService->generateEndorsementNumber($trans_type);
            $endorsement_no = $endorsementData->endorsement_no;
            $cover_serial_no = $endorsementData->serial_no;
            $this->_endorsement_no = $endorsement_no;

            $cover_no = $request->cover_no;
            $old_endorsement_no = $request->endorsement_no;

            $prevCoverRegister = CoverRegister::where('endorsement_no', $old_endorsement_no)->first();

            if (!$prevCoverRegister) {
                $prevCoverRegister = CoverRegister::where([
                    'cover_no' => $cover_no,
                    'transaction_type' => self::TRANSACTION_NEW
                ])->firstOrFail();
            }

            $orig_endorsement_no = $prevCoverRegister->orig_endorsement_no;

            $CoverRegister = new CoverRegister($prevCoverRegister->getAttributes());
            $this->populateEndorsementRegister(
                $CoverRegister,
                $request,
                $endorsement_no,
                $cover_serial_no,
                $orig_endorsement_no,
                $trans_type,
                $endorsementData
            );

            $CoverRegister->save();

            if ($this->isFacultativeBusiness($type_of_bus)) {
                $this->replicateFacultativePremiums($old_endorsement_no, $CoverRegister);
            }

            $this->createEndorsementNarration($request, $cover_no, $endorsement_no, $endorse_type);

            $this->replicateFromPrevious($old_endorsement_no, $request);

            DB::commit();

            return ['endorsement_no' => $this->_endorsement_no];
        } catch (\Exception $e) {
            DB::rollBack();

            logger()->error('Error in saveCoverEndorsement', [
                'cover_no' => $request->cover_no ?? null,
                'endorsement_no' => $request->endorsement_no ?? null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function deleteCoverData($coverNo, $endorsementNo)
    {
        DB::beginTransaction();
        try {
            $models = [
                CoverAttachment::class,
                CoverClass::class,
                CoverClause::class,
                CoverDebit::class,
                CoverInstallments::class,
                CoverPremium::class,
                CoverPremtype::class,
                CoverReinclass::class,
                CoverRisk::class,
                CoverReinLayer::class,
                CoverReinProp::class,
                CoverRipart::class,
                CoverSlipWording::class,
                ClaimRegister::class,
                CustomerAccDet::class,
                ReinNote::class,
                EndorsementNarration::class
            ];

            foreach ($models as $model) {
                $model::where([
                    'cover_no' => $coverNo,
                    'endorsement_no' => $endorsementNo
                ])->delete();
            }

            // Delete the cover register itself
            CoverRegister::where([
                'cover_no' => $coverNo,
                'endorsement_no' => $endorsementNo
            ])->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            logger()->error('Error deleting cover data', [
                'cover_no' => $coverNo,
                'endorsement_no' => $endorsementNo,
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }

    private function fetchCoverRelatedData($endorsement_no, $class_code)
    {
        $coverpart = CoverRipart::where('endorsement_no', $endorsement_no)->get();
        $coverReinclass = CoverReinclass::where('endorsement_no', $endorsement_no)->get();

        $coverTreaties = CoverPremtype::join('treaty_types', 'cover_premtypes.treaty', '=', 'treaty_types.treaty_code')
            ->where('cover_premtypes.endorsement_no', $endorsement_no)
            ->distinct('cover_premtypes.treaty')
            ->get(['cover_premtypes.treaty', 'treaty_types.treaty_name']);

        $treatyClasses = CoverPremtype::join('treaty_types', 'cover_premtypes.treaty', '=', 'treaty_types.treaty_code')
            ->where('cover_premtypes.endorsement_no', $endorsement_no)
            ->get([
                'cover_premtypes.premtype_name as class_name',
                'cover_premtypes.comm_rate as commission',
                'cover_premtypes.premtype_code as class_code',
            ]);

        $clauses = ClauseParam::where('status', 'A')
            ->where('class_code', $class_code)
            ->get();

        $selected_clauses = CoverClause::where('endorsement_no', $endorsement_no)->get();
        $endorsementNarration = EndorsementNarration::where('endorsement_no', $endorsement_no)->get();

        return [$coverpart, $coverReinclass, $coverTreaties, $treatyClasses, $clauses, $selected_clauses, $endorsementNarration];
    }

    private function getCachedInsuranceClasses()
    {
        return Cache::remember('insurance_classes_active', self::CACHE_TTL, function () {
            return Classes::where('status', 'A')->get();
        });
    }

    private function getCachedBusinessType($bus_type_id)
    {
        return Cache::remember("business_type_{$bus_type_id}", self::CACHE_TTL, function () use ($bus_type_id) {
            return BusinessType::where('bus_type_id', $bus_type_id)->first();
        });
    }

    private function getCachedScheduleHeaders()
    {
        return Cache::remember('schedule_headers', self::CACHE_TTL, function () {
            return ScheduleHeader::orderBy('position')->get();
        });
    }

    private function getScheduleHeadersForCover(CoverRegister $cover)
    {
        $prospect = $this->getProspectOpportunityForCover($cover);
        $classCode = $prospect->classcode ?? $cover->class_code;
        $classGroup = $prospect->class_group
            ?? $cover->class_group_code
            ?? ($classCode ? Classes::where('class_code', $classCode)->value('class_group_code') : null);
        $businessTypeAliases = $this->normalizeScheduleBusinessTypes(
            $prospect->type_of_bus ?? $cover->type_of_bus
        );

        $query = ScheduleHeader::query();
        $hasFilter = false;

        if ($classCode !== null && $classCode !== '') {
            if (Schema::hasColumn('schedule_headers', 'class')) {
                $query->where('class', $classCode);
                $hasFilter = true;
            } elseif (Schema::hasColumn('schedule_headers', 'class_code')) {
                $query->where('class_code', $classCode);
                $hasFilter = true;
            }
        }

        if ($classGroup !== null && $classGroup !== '') {
            if (Schema::hasColumn('schedule_headers', 'class_group')) {
                $query->where('class_group', $classGroup);
                $hasFilter = true;
            } elseif (Schema::hasColumn('schedule_headers', 'class_group_code')) {
                $query->where('class_group_code', $classGroup);
                $hasFilter = true;
            }
        }

        if (!empty($businessTypeAliases)) {
            if (Schema::hasColumn('schedule_headers', 'business_type')) {
                $query->whereIn('business_type', $businessTypeAliases);
                $hasFilter = true;
            } elseif (Schema::hasColumn('schedule_headers', 'bus_type')) {
                $query->whereIn('bus_type', $businessTypeAliases);
                $hasFilter = true;
            } elseif (Schema::hasColumn('schedule_headers', 'type_of_bus')) {
                $query->whereIn('type_of_bus', $businessTypeAliases);
                $hasFilter = true;
            }
        }

        if (!$hasFilter) {
            return $this->getCachedScheduleHeaders();
        }

        return $query->orderBy('position')->get();
    }

    private function getProspectOpportunityForCover(CoverRegister $cover): ?PipelineOpportunity
    {
        $prospectId = trim((string) ($cover->prospect_id ?? ''));
        if ($prospectId === '') {
            return null;
        }

        return PipelineOpportunity::query()
            ->where('opportunity_id', $prospectId)
            ->orWhere(function ($query) use ($prospectId) {
                if (is_numeric($prospectId)) {
                    $query->where('id', (int) $prospectId);
                }
            })
            ->first();
    }

    private function normalizeScheduleBusinessTypes($typeOfBus): array
    {
        $type = strtoupper(trim((string) $typeOfBus));

        return match ($type) {
            'FPR', 'FNP', 'FAC', 'FACULTATIVE' => ['FAC', 'FPR', 'FNP', 'FACULTATIVE'],
            'TPR', 'TNP', 'TRT', 'TREATY' => ['TRT', 'TPR', 'TNP', 'TREATY'],
            default => [],
        };
    }

    private function getCachedWhtRates()
    {
        return Cache::remember('wht_rates', self::CACHE_TTL, function () {
            return WhtRate::all();
        });
    }

    private function getCachedPaymentMethods()
    {
        return Cache::remember('payment_methods', self::CACHE_TTL, function () {
            return PayMethod::all();
        });
    }


    private function getCachedPaymentTerms()
    {
        return Cache::remember('premium_pay_terms', self::CACHE_TTL, function () {
            return PremiumPayTerm::all();
        });
    }

    private function getCachedSystemProcess($nice_name)
    {
        return Cache::remember("system_process_{$nice_name}", self::CACHE_TTL, function () use ($nice_name) {
            return SystemProcess::where('nice_name', $nice_name)->first();
        });
    }

    private function getCachedSystemProcessAction($nice_name)
    {
        return Cache::remember("system_process_action_{$nice_name}", self::CACHE_TTL, function () use ($nice_name) {
            return SystemProcessAction::where('nice_name', $nice_name)->first();
        });
    }

    private function getVerifiers()
    {
        return User::where('user_name', '<>', Auth::user()->user_name)->get();
    }

    private function getCustomerTypes($type_of_bus)
    {
        return match ($type_of_bus) {
            self::TYPE_TREATY_PROPORTIONAL,
            self::TYPE_TREATY_NON_PROPORTIONAL => ['REINCO'],
            self::TYPE_FACULTATIVE_PROPORTIONAL,
            self::TYPE_FACULTATIVE_NON_PROPORTIONAL => ['REINCO', 'INSCO'],
            default => []
        };
    }

    private function getReinsurers(array $cusType)
    {
        if (empty($cusType)) {
            return collect();
        }

        return DB::table('customers')
            ->join('customer_types', function ($join) {
                $join->on(
                    'customer_types.type_id',
                    '=',
                    DB::raw("ANY (SELECT json_array_elements_text(customers.customer_type)::int)")
                );
            })
            ->select('customers.customer_id', 'customers.name')
            ->whereIn('customer_types.code', $cusType)
            ->distinct()
            ->get();
    }

    private function calculatePremiumTotals($endorsement_no)
    {
        $totals = CoverPremium::where('endorsement_no', $endorsement_no)
            ->selectRaw("
                SUM(CASE WHEN entry_type_descr = ? THEN final_amount ELSE 0 END) as premium,
                SUM(CASE WHEN entry_type_descr = ? THEN final_amount ELSE 0 END) as commission,
                SUM(CASE WHEN entry_type_descr = ? THEN final_amount ELSE 0 END) as claim,
                SUM(CASE WHEN transaction_type = ? AND entry_type_descr = ? THEN final_amount ELSE 0 END) as mdp,
                SUM(CASE WHEN entry_type_descr = ? THEN final_amount ELSE 0 END) as premiumTax,
                SUM(CASE WHEN entry_type_descr = ? THEN final_amount ELSE 0 END) as riTax,
                SUM(CASE WHEN dr_cr = ? THEN final_amount ELSE 0 END) as totalDr,
                SUM(CASE WHEN dr_cr = ? THEN final_amount ELSE 0 END) as totalCr
            ", [
                self::ENTRY_PREMIUM,
                self::ENTRY_COMMISSION,
                self::ENTRY_CLAIM,
                self::ENTRY_MINIMUM_DEPOSIT,
                self::ENTRY_MINIMUM_DEPOSIT,
                self::ENTRY_PREMIUM_TAX,
                self::ENTRY_REINSURANCE_TAX,
                self::DR,
                self::CR
            ])
            ->first();

        return [
            'premium' => $totals->premium ?? 0,
            'commission' => $totals->commission ?? 0,
            'claim' => $totals->claim ?? 0,
            'mdp' => $totals->mdp ?? 0,
            'premiumTax' => $totals->premiumTax ?? 0,
            'riTax' => $totals->riTax ?? 0,
            'totalDr' => $totals->totalDr ?? 0,
            'totalCr' => $totals->totalCr ?? 0,
        ];
    }

    private function calculateInstallmentDetails($cover, $debitsCount, $premiumTotals)
    {
        $totalInstallments = (int) $cover->no_of_installments;
        $remaining = $totalInstallments - $debitsCount;
        $next = 0;
        $amount = 0;

        if ($remaining > 0) {
            $next = $debitsCount + 1;

            if (
                $this->isFacultativeBusiness($cover->type_of_bus) ||
                $this->isTreatyBusiness($cover->type_of_bus)
            ) {
                $amount = $premiumTotals['totalDr'] - $premiumTotals['totalCr'];
            }
        }

        return [
            'remaining' => $remaining,
            'next' => $next,
            'amount' => $amount
        ];
    }

    private function getCoverReinpProps(CoverRegister $cover)
    {
        $coveReinProp = CoverReinProp::where('endorsement_no', $cover->endorsement_no)->first();
        $reinsurerShareTotal = (float) CoverRipart::where('endorsement_no', $cover->endorsement_no)
            ->where('cover_no', $cover->cover_no)
            ->sum('share');

        if (
            $reinsurerShareTotal <= 0 &&
            !empty($cover->orig_endorsement_no) &&
            $cover->orig_endorsement_no !== $cover->endorsement_no
        ) {
            $reinsurerShareTotal = (float) CoverRipart::where('endorsement_no', $cover->orig_endorsement_no)
                ->where('cover_no', $cover->cover_no)
                ->sum('share');
        }

        $formattedReinsurerShare = number_format($reinsurerShareTotal, 2) . '%';
        $coverreinprop = [];
        if ($coveReinProp) {
            $coverreinprop = [
                'treaty_limit' => number_format($coveReinProp->treaty_amount, 2),
                'treaty_capacity' =>  number_format($coveReinProp->treaty_limit, 2),
                'no_of_lines' => number_format($coveReinProp->no_of_lines, 2),
                'item_description' => $coveReinProp->item_description,
                'total_reinsurers' => $formattedReinsurerShare
            ];
        } else {
            $coverreinprop = [
                'treaty_limit' =>  '0.00',
                'treaty_capacity' => '0.00',
                'no_of_lines' => '0',
                'item_description' => '',
                'total_reinsurers' => $formattedReinsurerShare
            ];
        }

        return $coverreinprop;
    }

    private function getReinLayerDetails($cover)
    {
        $mdpAmount = CoverReinLayer::where('endorsement_no', $cover->endorsement_no)
            ->sum('min_deposit');

        $reinLayersCount = CoverReinLayer::where('endorsement_no', $cover->orig_endorsement_no)
            ->groupBy('layer_no')
            ->count();

        $reinLayers = DB::table('coverreinlayers')
            ->select('cover_no', 'endorsement_no', 'layer_no', DB::raw('SUM(min_deposit) as min_deposit'))
            ->where('endorsement_no', $cover->orig_endorsement_no)
            ->groupBy('cover_no', 'endorsement_no', 'layer_no')
            ->get();

        return [
            'mdpAmount' => $mdpAmount,
            'count' => $reinLayersCount,
            'layers' => $reinLayers
        ];
    }

    private function calculateReinsurerAmounts($cover, $ripart, $request)
    {
        if ($this->isFacultativeBusiness($cover->type_of_bus)) {
            return $this->calculateFacultativeAmounts($cover, $request);
        }

        if ($this->isTreatyBusiness($cover->type_of_bus)) {
            return $this->calculateTreatyAmounts($cover, $ripart, $request);
        }

        return $this->getEmptyAmounts();
    }

    private function calculateFacultativeAmounts($cover, $request)
    {
        $total_sum_insured = $cover->total_sum_insured ?? 0;
        $total_premium = $cover->rein_premium ?? 0;
        $total_commission = $cover->rein_comm_amount ?? 0;

        $comm_rate = $this->parseNumeric($request->comm_rate);
        $wht_rate = $this->parseNumeric($request->wht_rate);
        $fronting_rate = $this->parseNumeric($request->fronting_rate);
        $sum_insured = $this->parseNumeric($request->sum_insured);
        $premium = $this->parseNumeric($request->premium);
        $commission = ($comm_rate / 100) * $premium;
        $brokerage_comm_amt = $this->parseNumeric($request->brokerage_comm_amt);

        $wht_amt = $wht_rate > 0
            ? max(0, ceil(($wht_rate / 100) * ($premium - $commission)))
            : 0;

        $fronting_amt = $fronting_rate > 0
            ? max(0, ceil(($fronting_rate / 100) * ($premium - $commission)))
            : 0;

        $net_amount = max(0, $premium - $commission - $brokerage_comm_amt - $wht_amt - $fronting_amt);

        return [
            'total_sum_insured' => $total_sum_insured,
            'sum_insured' => $sum_insured,
            'total_premium' => $total_premium,
            'premium' => $premium,
            'total_commission' => $total_commission,
            'comm_rate' => $comm_rate,
            'commission' => $commission,
            'wht_rate' => $wht_rate,
            'wht_amt' => $wht_amt,
            'fronting_rate' => $fronting_rate,
            'fronting_amt' => $fronting_amt,
            'brokerage_comm_amt' => $brokerage_comm_amt,
            'net_amount' => $net_amount,
        ];
    }

    private function calculateTreatyAmounts($cover, $ripart, $request)
    {
        $premiumTotals = $this->calculatePremiumTotals($cover->endorsement_no);

        $total_premium = $premiumTotals['premium'];
        $total_commission = $premiumTotals['commission'];
        $total_sum_insured = $premiumTotals['totalDr']; // Assuming SUM is included

        $share = (float) $request->share;
        $sum_insured = max(0, ceil($total_sum_insured * $share / 100));
        $premium = max(0, ceil($total_premium * $share / 100));
        $commission = max(0, ceil($total_commission * $share / 100));

        $fronting_rate = $ripart->fronting_rate ?? 0;
        $fronting_amt = max(0, ceil(($fronting_rate / 100) * ($premium - $commission)));

        $wht_rate = $this->parseNumeric($request->wht_rate);
        $wht_amt = max(0, ceil(($wht_rate / 100) * ($premium - $commission)));

        $net_amount = max(0, $premium - $commission - $wht_amt - $fronting_amt);

        return [
            'total_sum_insured' => $total_sum_insured,
            'sum_insured' => $sum_insured,
            'total_premium' => $total_premium,
            'premium' => $premium,
            'total_commission' => $total_commission,
            'comm_rate' => $request->comm_rate ?? 0,
            'commission' => $commission,
            'wht_rate' => $wht_rate,
            'wht_amt' => $wht_amt,
            'fronting_rate' => $fronting_rate,
            'fronting_amt' => $fronting_amt,
            'brokerage_comm_amt' => 0,
            'net_amount' => $net_amount,
        ];
    }

    private function getEmptyAmounts()
    {
        return [
            'total_sum_insured' => 0,
            'sum_insured' => 0,
            'total_premium' => 0,
            'premium' => 0,
            'total_commission' => 0,
            'comm_rate' => 0,
            'commission' => 0,
            'wht_rate' => 0,
            'wht_amt' => 0,
            'fronting_rate' => 0,
            'fronting_amt' => 0,
            'brokerage_comm_amt' => 0,
            'net_amount' => 0,
        ];
    }

    public function isFacultativeBusiness($type_of_bus)
    {
        return in_array($type_of_bus, [
            self::TYPE_FACULTATIVE_PROPORTIONAL,
            self::TYPE_FACULTATIVE_NON_PROPORTIONAL
        ]);
    }

    public function isTreatyBusiness($type_of_bus)
    {
        return in_array($type_of_bus, [
            self::TYPE_TREATY_PROPORTIONAL,
            self::TYPE_TREATY_NON_PROPORTIONAL
        ]);
    }

    private function parseNumeric($value, $default = 0.0)
    {
        if (is_null($value) || $value === '') {
            return $default;
        }
        return (float) str_replace(',', '', $value);
    }

    private function safeDivide($numerator, $denominator, $default = 0.0)
    {
        if (abs($denominator) < self::FLOAT_COMPARISON_EPSILON) {
            return $default;
        }
        return $numerator / $denominator;
    }

    private function createReinNotes($cover, $ripart, $amounts)
    {
        $premItemTypes = [
            self::ENTRY_PREMIUM => [
                'descr' => 'Gross Premium',
                'dr_cr' => self::CR,
                'tax_rate' => $ripart->share ?? 0,
                'total_amount' => $amounts['total_premium'],
                'amount' => $amounts['premium'],
            ],
            self::ENTRY_BROKERAGE_COMMISSION => [
                'descr' => 'Brokerage Commission',
                'dr_cr' => self::DR,
                'amount' => $amounts['brokerage_comm_amt'],
                'tax_rate' => 0,
                'total_amount' => $amounts['premium'],
            ],
            self::ENTRY_COMMISSION => [
                'descr' => 'Commission',
                'dr_cr' => self::DR,
                'tax_rate' => $amounts['comm_rate'],
                'amount' => $amounts['commission'],
                'total_amount' => $amounts['premium'],
            ],
            self::ENTRY_WITHHOLDING_TAX => [
                'descr' => 'Withholding Tax',
                'dr_cr' => self::DR,
                'tax_rate' => $amounts['wht_rate'],
                'amount' => $amounts['wht_amt'],
                'total_amount' => $amounts['premium'] - $amounts['commission'],
            ],
            self::ENTRY_FRONTING_FEE => [
                'descr' => 'Fronting Fees',
                'dr_cr' => self::CR,
                'tax_rate' => $amounts['fronting_rate'],
                'amount' => $amounts['fronting_amt'],
                'total_amount' => $amounts['premium'] - $amounts['commission'],
            ],
        ];

        foreach ($premItemTypes as $key => $premItemType) {
            if ($premItemType['amount'] <= 0) {
                continue;
            }

            $tran_no = DB::transaction(function () use ($cover) {
                $max = DB::table('rein_notes')
                    ->whereNull('deleted_at')
                    ->where('endorsement_no', $cover->endorsement_no)
                    ->max('tran_no');
                return ($max ?? 0) + 1;
            });

            $ln_no = DB::transaction(function () use ($cover, $key) {
                $count = DB::table('rein_notes')
                    ->whereNull('deleted_at')
                    ->where('endorsement_no', $cover->endorsement_no)
                    ->where('transaction_type', $cover->transaction_type)
                    ->where('entry_type_descr', $key)
                    ->count();
                return $count + 1;
            });

            ReinNote::create([
                'cover_no' => $cover->cover_no,
                'endorsement_no' => $cover->endorsement_no,
                'partner_no' => $ripart->partner_no,
                'transaction_type' => $cover->transaction_type,
                'account_year' => $this->_year,
                'account_month' => $this->_month,
                'share' => (float) ($ripart->share ?? 0),
                'created_by' => Auth::user()->user_name,
                'updated_by' => Auth::user()->user_name,
                'tran_no' => $tran_no,
                'ln_no' => $ln_no,
                'entry_type_descr' => $key,
                'item_title' => $premItemType['descr'],
                'dr_cr' => $premItemType['dr_cr'],
                'rate' => $premItemType['tax_rate'] ?? 0,
                'total_gross' => $premItemType['total_amount'],
                'gross' => $premItemType['amount'],
                'net_amt' => $premItemType['amount'],
            ]);
        }
    }

    public function generateExtDocNumber()
    {
        return $this->sequenceService->generateDocumentNumber();
    }

    public function getNextSequence($busType, $branchCode, $year)
    {
        $lastCover = CoverRegister::where('cover_no', 'LIKE', "$busType/$branchCode/$year/%")
            ->orderBy('cover_no', 'desc')
            ->first();

        if (!$lastCover) {
            return 1;
        }

        $parts = explode('/', $lastCover->cover_no);
        return isset($parts[3]) ? (int)$parts[3] + 1 : 1;
    }

    private function prepareBusinessTypeData($request, $type_of_bus, $customer, $treatytype)
    {
        if ($this->isFacultativeBusiness($type_of_bus)) {
            return $this->prepareFacultativeData($request);
        }

        if ($type_of_bus === self::TYPE_TREATY_NON_PROPORTIONAL) {
            return $this->prepareTreatyNonPropData($request, $customer, $treatytype);
        }

        if ($type_of_bus === self::TYPE_TREATY_PROPORTIONAL) {
            return $this->prepareTreatyPropData($request, $customer, $treatytype);
        }

        throw new \Exception('Invalid business type');
    }

    private function prepareFacultativeData($request)
    {
        $classcode = $request->classcode;
        $insured_name = $request->insured_name;

        $class_name = Classes::select('class_name')
            ->where('class_code', $classcode)
            ->firstOrFail();

        $treaty_name = $class_name->class_name . ' FACULTATIVE';
        $date_offered = $request->fac_date_offered;
        $share_offered = $request->fac_share_offered;
        $rein_premium = $this->parseNumeric($request->rein_premium);

        if ($request->brokerage_comm_type === 'R') {
            $brokerage_comm_rate = $this->parseNumeric($request->brokerage_comm_rate);
            $brokerage_comm_amt = ($brokerage_comm_rate / 100) * $rein_premium;
        } else {
            $brokerage_comm_amt = $this->parseNumeric($request->brokerage_comm_amt);
            $brokerage_comm_rate = $this->safeDivide($brokerage_comm_amt, $rein_premium) * 100;
        }

        return [
            'classcode' => $classcode,
            'insured_name' => $insured_name,
            'treaty_name' => $treaty_name,
            'date_offered' => $date_offered,
            'share_offered' => $share_offered,
            'rein_premium' => $rein_premium,
            'brokerage_comm_rate' => $brokerage_comm_rate,
            'brokerage_comm_amt' => $brokerage_comm_amt,
        ];
    }

    private function prepareTreatyNonPropData($request, $customer, $treatytype)
    {
        $brokerage_comm_rate = $request->brokerage_comm_rate;
        $reinclass_code = $request->reinclass_code;
        $classcode = self::TREATY_CODE_TRT;
        $insured_name = $customer->name;

        $reinclass = ReinsClass::whereIn('class_code', $reinclass_code)
            ->pluck('class_name')
            ->toArray();

        $treaty_name = implode('-', $reinclass) . ' ' .
            ($treatytype ? $treatytype->treaty_name : '') . ' TREATY';

        $date_offered = $request->date_offered;
        $share_offered = $request->share_offered;

        return [
            'classcode' => $classcode,
            'insured_name' => $insured_name,
            'treaty_name' => $treaty_name,
            'date_offered' => $date_offered,
            'share_offered' => $share_offered,
            'brokerage_comm_rate' => $brokerage_comm_rate,
            'reinclass_code' => $reinclass_code,
        ];
    }

    private function prepareTreatyPropData($data, $customer, $treatytype)
    {
        $brokerage_comm_rate = $data->treaty_brokerage_comm_rate;
        $treaty_reinclass = $data->treaty_reinclass;
        $classcode = self::TREATY_CODE_TRT;
        $insured_name = $customer->name;

        $type_of_treaty = $treatytype?->type_of_bus === self::TYPE_TREATY_NON_PROPORTIONAL
            ? 'Non-Proportional'
            : 'Proportional';

        $reinclass = ReinsClass::whereIn('class_code', $treaty_reinclass)
            ->pluck('class_name')
            ->toArray();

        $treaty_name = sprintf(
            '%s %s Treaty - %s',
            $treatytype?->treaty_name ?? '',
            $type_of_treaty,
            implode(', ', $reinclass)
        );

        $treaty_name = ucwords(strtolower(trim(preg_replace('/\s+/', ' ', $treaty_name))));

        $date_offered = $data->date_offered;
        $share_offered = $data->share_offered;

        return [
            'classcode' => $classcode,
            'insured_name' => $insured_name,
            'treaty_name' => $treaty_name,
            'date_offered' => $date_offered,
            'share_offered' => $share_offered,
            'brokerage_comm_rate' => $brokerage_comm_rate,
            'treaty_reinclass' => $treaty_reinclass,
        ];
    }

    private function populateCoverRegister(
        $CoverRegister,
        $data,
        $cover_no,
        $endorsement_no,
        $orig_endorsement_no,
        $cover_serial_no,
        $businessData
    ) {
        $ppw = null;
        if ($data->premium_payment_term) {
            $ppw = PremiumPayTerm::where('pay_term_code', $data->premium_payment_term)->first();
        }

        $branchcode = str_pad((int) $data->branchcode, 3, '0', STR_PAD_LEFT);

        $CoverRegister->cover_serial_no = $cover_serial_no;
        $CoverRegister->customer_id = $data->customer_id;
        $CoverRegister->type_of_bus = $data->type_of_bus;
        $CoverRegister->cover_no = $cover_no;
        $CoverRegister->endorsement_no = $endorsement_no;
        $CoverRegister->orig_endorsement_no = $orig_endorsement_no;
        $CoverRegister->transaction_type = $data->trans_type;
        $CoverRegister->premium_payment_code = $data->premium_payment_term;
        $CoverRegister->premium_payment_days = $ppw ? $ppw->premium_payment_days : 0;
        $CoverRegister->branch_code = $branchcode;
        $CoverRegister->broker_code = $data->brokercode ?: 0;
        $CoverRegister->cover_type = $data->covertype;
        $CoverRegister->class_code = $businessData['classcode'];
        $CoverRegister->class_group_code = $data->class_group;
        $CoverRegister->insured_name = $businessData['insured_name'];
        $CoverRegister->effective_date = $data->cover_from;
        $CoverRegister->cover_from = $data->cover_from;
        $CoverRegister->cover_to = $data->cover_to;
        $CoverRegister->account_year = $this->_year;
        $CoverRegister->account_month = $this->_month;
        $CoverRegister->binder_cov_no = $data->binder_cover_no;
        $CoverRegister->pay_method_code = $data->pay_method;
        $CoverRegister->currency_code = $data->currency_code;
        $CoverRegister->currency_rate = $data->today_currency;
        $CoverRegister->type_of_sum_insured = $data->sum_insured_type;
        $CoverRegister->rein_premium = $businessData['rein_premium'] ?? 0;
        $CoverRegister->total_sum_insured = $this->parseNumeric($data->total_sum_insured);
        $CoverRegister->cedant_premium = $this->parseNumeric($data->cede_premium);
        $CoverRegister->apply_eml = $data->apply_eml ?? 'N';
        $CoverRegister->eml_rate = $this->parseNumeric($data->eml_rate);
        $CoverRegister->eml_amount = $this->parseNumeric($data->eml_amt);
        $CoverRegister->effective_sum_insured = $this->parseNumeric($data->effective_sum_insured);
        $CoverRegister->cedant_comm_rate = $data->comm_rate;
        $CoverRegister->cedant_comm_amount = $this->parseNumeric($data->comm_amt);
        $CoverRegister->rein_comm_type = $data->reins_comm_type;
        $CoverRegister->rein_comm_rate = $this->parseNumeric($data->reins_comm_rate);
        $CoverRegister->brokerage_comm_rate = $businessData['brokerage_comm_rate'] ?? 0;
        $CoverRegister->brokerage_comm_amt = $businessData['brokerage_comm_amt'] ?? 0;
        $CoverRegister->brokerage_comm_type = $data->brokerage_comm_type;
        $CoverRegister->reinsurer_per_treaty = $data->reinsurer_per_treaty;
        $CoverRegister->rein_comm_amount = $this->parseNumeric($data->reins_comm_amt);
        $CoverRegister->division_code = $data->division;
        $CoverRegister->vat_charged = $data->vat_charged;
        $CoverRegister->treaty_type = $data->treatytype;
        $CoverRegister->risk_details = $data->risk_details;
        $CoverRegister->cover_title = $businessData['treaty_name'];
        $CoverRegister->date_offered = $businessData['date_offered'];
        $CoverRegister->share_offered = (float) $businessData['share_offered'];
        $CoverRegister->no_of_installments = (int) $data->no_of_installments;
        // $CoverRegister->territorial_scope = $data->territorial_scope;
        $CoverRegister->basis_of_acceptance = $data->basis_of_acceptance;
        $CoverRegister->port_prem_rate = $this->parseNumeric($data->port_prem_rate);
        $CoverRegister->port_loss_rate = $this->parseNumeric($data->port_loss_rate);
        $CoverRegister->profit_comm_rate = $this->parseNumeric($data->profit_comm_rate);
        $CoverRegister->mgnt_exp_rate = $this->parseNumeric($data->mgnt_exp_rate);
        $CoverRegister->deficit_yrs = $this->parseNumeric($data->deficit_yrs);
        $CoverRegister->deposit_frequency = $data->deposit_frequency ?: 0;
        $CoverRegister->prem_tax_rate = $this->parseNumeric($data->prem_tax_rate);
        $CoverRegister->ri_tax_rate = $this->parseNumeric($data->ri_tax_rate);
        $CoverRegister->status = 'A';
        $CoverRegister->verified = null;
        $CoverRegister->prospect_id = $data->prospect_id;
        $CoverRegister->created_at = now();
        $CoverRegister->updated_at = now();
        $CoverRegister->created_by = Auth::user()->user_name;
        $CoverRegister->updated_by = Auth::user()->user_name;
    }

    private function createBusinessTypeRecords($data, $type_of_bus, $cover_no, $endorsement_no, $CoverRegister)
    {
        if ($type_of_bus === self::TYPE_TREATY_NON_PROPORTIONAL) {
            $this->createTNPRecords($data, $cover_no, $endorsement_no);
        } elseif ($type_of_bus === self::TYPE_TREATY_PROPORTIONAL) {
            $this->createTPRRecords($data, $cover_no, $endorsement_no);
        } elseif ($this->isFacultativeBusiness($type_of_bus)) {
            $this->createFacultativeRecords($data, $cover_no, $endorsement_no, $CoverRegister);
        }
    }

    private function createTNPRecords($request, $cover_no, $endorsement_no)
    {
        $reinclass_code = $request->reinclass_code;

        foreach ($reinclass_code as $reinclass) {
            CoverReinclass::create([
                'cover_no' => $cover_no,
                'endorsement_no' => $endorsement_no,
                'reinclass' => $reinclass,
                'created_by' => Auth::user()->user_name,
                'updated_by' => Auth::user()->user_name,
            ]);
        }

        $this->createNonPropLayers($request, $cover_no, $endorsement_no);
    }

    private function createNonPropLayers($request, $cover_no, $endorsement_no)
    {
        $indemnity_limits = $request->indemnity_treaty_limit;
        $underlying_limits = $request->underlying_limit;
        $egnpi = $request->egnpi;
        $method = $request->method;
        $payment_frequency = $request->deposit_frequency ?: 0;
        $layer_nos = $request->layer_no;
        $nonprop_reinclass = $request->nonprop_reinclass;
        $reinstatement_types = $request->reinstatement_type;
        $reinstatement_values = $request->reinstatement_value;

        $item_no = 1;

        foreach ($indemnity_limits as $index => $indemnity_limit) {
            if ($index > 0 && $layer_nos[$index - 1] == $layer_nos[$index]) {
                $item_no++;
            } else {
                $item_no = 1;
            }

            $layerData = [
                'cover_no' => $cover_no,
                'endorsement_no' => $endorsement_no,
                'layer_no' => $layer_nos[$index],
                'indemnity_limit' => $this->parseNumeric($indemnity_limit),
                'underlying_limit' => $this->parseNumeric($underlying_limits[$index]),
                'egnpi' => $this->parseNumeric($egnpi[$index]),
                'method' => $method,
                'payment_frequency' => $payment_frequency,
                'reinclass' => $nonprop_reinclass[$index],
                'reinstatement_type' => $reinstatement_types[$index],
                'reinstatement_value' => $this->parseNumeric($reinstatement_values[$index]),
                'item_no' => $item_no,
            ];

            if ($method === 'F') {
                $layerData['flat_rate'] = $this->parseNumeric($request->flat_rate[$index]);
                $layerData['min_bc_rate'] = 0;
                $layerData['max_bc_rate'] = $this->parseNumeric($request->flat_rate[$index]);
                $layerData['upper_adj'] = $this->parseNumeric($request->flat_rate[$index]);
                $layerData['lower_adj'] = 0;
            } else {
                $layerData['flat_rate'] = 0;
                $layerData['min_bc_rate'] = $this->parseNumeric($request->min_bc_rate[$index]);
                $layerData['max_bc_rate'] = $this->parseNumeric($request->max_bc_rate[$index]);
                $layerData['upper_adj'] = $this->parseNumeric($request->upper_adj[$index]);
                $layerData['lower_adj'] = $this->parseNumeric($request->lower_adj[$index]);
            }

            $layerData['min_deposit'] = $this->parseNumeric($request->min_deposit[$index]);

            CoverReinLayer::create($layerData);
        }
    }

    private function createTPRRecords($data, $cover_no, $endorsement_no)
    {
        $treaty_reinclass = $data->treaty_reinclass;

        if ($treaty_reinclass && !empty($treaty_reinclass)) {
            foreach ($treaty_reinclass as $index => $treaty_class) {

                CoverReinclass::create([
                    'cover_no' => $cover_no,
                    'endorsement_no' => $endorsement_no,
                    'reinclass' => $treaty_class,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);

                $this->createPropRecords($data, $cover_no, $endorsement_no, $treaty_class, $index);
            }
        }

        $this->createPremiumTypes($data, $cover_no, $endorsement_no);
    }

    private function createPropRecords($data, $cover_no, $endorsement_no, $treaty_class, $index)
    {
        $retention_per = $this->parseNumeric($data->retention_per[$index] ?? null);
        $treaty_reice = $this->parseNumeric($data->treaty_reice[$index] ?? null);
        $surp_retention_amt = $this->parseNumeric($data->surp_retention_amt[$index] ?? null);
        $no_of_lines = $this->parseNumeric($data->no_of_lines[$index] ?? null);
        $surp_treaty_limit = $this->parseNumeric($data->surp_treaty_limit[$index] ?? null);
        $quota_retention_amt = $this->parseNumeric($data->quota_retention_amt[$index] ?? null);
        $quota_share_total_limit = $this->parseNumeric($data->quota_share_total_limit[$index] ?? null);
        $estimated_income = $this->parseNumeric($data->estimated_income[$index] ?? null);
        $cashloss_limit = $this->parseNumeric($data->cashloss_limit[$index] ?? null);

        $count = CoverReinProp::where('cover_no', $cover_no)
            ->where('endorsement_no', $endorsement_no)
            ->count();

        $baseData = [
            'cover_no' => $cover_no,
            'endorsement_no' => $endorsement_no,
            'reinclass' => $treaty_class,
            'retention_rate' => $retention_per,
            'treaty_rate' => $treaty_reice,
            'no_of_lines' => $no_of_lines,
            'port_prem_rate' => 0,
            'port_loss_rate' => 0,
            'profit_comm_rate' => 0,
            'mgnt_exp_rate' => 0,
            'deficit_yrs' => 0,
            'estimated_income' => $estimated_income,
            'cashloss_limit' => $cashloss_limit,
            'created_by' => Auth::user()->user_name,
            'updated_by' => Auth::user()->user_name,
        ];

        $treatytype = $data->treatytype;

        if ($treatytype === 'SURP') {
            $this->upsertCoverReinProp(array_merge($baseData, [
                'item_no' => $count + 1,
                'item_description' => 'SURPLUS',
                'retention_amount' => $surp_retention_amt,
                'treaty_amount' => $surp_treaty_limit,
                'treaty_limit' => $surp_retention_amt + $surp_treaty_limit,
            ]));
        } elseif ($treatytype === 'QUOT') {
            $this->upsertCoverReinProp(array_merge($baseData, [
                'item_no' => $count + 1,
                'item_description' => 'QUOTA',
                'retention_amount' => $quota_retention_amt,
                'treaty_amount' => $quota_share_total_limit,
                'treaty_limit' => $quota_retention_amt + $quota_share_total_limit,
            ]));
        } elseif ($treatytype === 'SPQT') {
            if ($quota_share_total_limit > 0) {
                $this->upsertCoverReinProp(array_merge($baseData, [
                    'item_no' => $count + 1,
                    'item_description' => 'QUOTA',
                    'retention_amount' => $quota_retention_amt,
                    'treaty_amount' => $quota_share_total_limit,
                    'treaty_limit' => $quota_retention_amt + $quota_share_total_limit,
                ]));
                $count++;
            }
            if ($surp_treaty_limit > 0) {
                $this->upsertCoverReinProp(array_merge($baseData, [
                    'item_no' => $count + 1,
                    'item_description' => 'SURPLUS',
                    'retention_amount' => $surp_retention_amt,
                    'treaty_amount' => $surp_treaty_limit,
                    'treaty_limit' => $surp_retention_amt + $surp_treaty_limit,
                ]));
            }
        }
    }

    private function upsertCoverReinProp(array $data): void
    {
        $identity = [
            'cover_no' => $data['cover_no'],
            'endorsement_no' => $data['endorsement_no'],
            'reinclass' => $data['reinclass'],
            'item_no' => $data['item_no'],
        ];

        $record = CoverReinProp::withTrashed()->where($identity)->first();

        if ($record) {
            $record->fill($data);
            if (method_exists($record, 'trashed') && $record->trashed()) {
                $record->deleted_at = null;
            }
            $record->save();
            return;
        }

        CoverReinProp::create($data);
    }

    private function createPremiumTypes($data, $cover_no, $endorsement_no)
    {
        $prem_type_reinclass = $data->prem_type_reinclass;
        $prem_type_treaty = $data->prem_type_treaty;
        $prem_type_code = $data->prem_type_code;
        // $prem_type_comm_rate = $data->prem_type_comm_rate;
        $flat_prem_type_comm_rate = $data->flat_prem_type_comm_rate;
        // $sliding_treaty_prem_type_comm_rate = $data->sliding_treaty_prem_type_comm_rate;

        if ($prem_type_reinclass && !empty($prem_type_reinclass)) {
            foreach ($prem_type_reinclass as $index => $reinclass) {
                $premtype_reinclass = ReinclassPremtype::where('reinclass', $reinclass)
                    ->where('premtype_code', $prem_type_code[$index])
                    ->first();

                CoverPremtype::create([
                    'cover_no' => $cover_no,
                    'endorsement_no' => $endorsement_no,
                    'reinclass' => $reinclass,
                    'treaty' => $prem_type_treaty[$index],
                    'premtype_code' => $prem_type_code[$index],
                    'treaty_commission_type' => $data->treaty_commission_type[$index],
                    'premtype_name' => $premtype_reinclass->premtype_name,
                    'comm_rate' => $flat_prem_type_comm_rate[$index],
                ]);
            }
        }
    }

    private function createFacultativeRecords($request, $cover_no, $endorsement_no, $CoverRegister)
    {
        $share_offered = (float) $request->fac_share_offered;
        $cede_premium = $this->parseNumeric($request->cede_premium);
        $comm_rate = $this->parseNumeric($request->comm_rate);

        // Gross Premium
        CoverPremium::create([
            'cover_no' => $cover_no,
            'endorsement_no' => $endorsement_no,
            'orig_endorsement_no' => $CoverRegister->orig_endorsement_no,
            'transaction_type' => $CoverRegister->transaction_type,
            'premium_type_code' => 0,
            'premtype_name' => 'Gross Premium',
            'quarter' => $this->_quarter,
            'entry_type_descr' => self::ENTRY_PREMIUM,
            'premium_type_order_position' => 1,
            'premium_type_description' => 'Gross Premium',
            'type_of_bus' => $request->type_of_bus,
            'class_code' => $request->classcode,
            'basic_amount' => $cede_premium,
            'apply_rate_flag' => 'Y',
            'treaty' => self::TREATY_CODE_FAC,
            'rate' => $share_offered,
            'dr_cr' => in_array($CoverRegister->transaction_type, [self::TRANSACTION_REFUND, self::TRANSACTION_CANCEL])
                ? self::CR
                : self::DR,
            'final_amount' => ($share_offered / 100) * $cede_premium,
            'created_by' => Auth::user()->user_name,
            'updated_by' => Auth::user()->user_name,
        ]);

        $adjusted_premium = ($share_offered / 100) * $cede_premium;
        $commission = ($comm_rate / 100) * $adjusted_premium;

        // Commission
        CoverPremium::create([
            'cover_no' => $cover_no,
            'endorsement_no' => $endorsement_no,
            'orig_endorsement_no' => $CoverRegister->orig_endorsement_no,
            'transaction_type' => $CoverRegister->transaction_type,
            'premium_type_code' => 0,
            'premtype_name' => 'Commission',
            'quarter' => $this->_quarter,
            'entry_type_descr' => self::ENTRY_COMMISSION,
            'premium_type_order_position' => 2,
            'premium_type_description' => 'Commission',
            'type_of_bus' => $request->type_of_bus,
            'class_code' => $request->classcode,
            'treaty' => self::TREATY_CODE_FAC,
            'basic_amount' => $adjusted_premium,
            'apply_rate_flag' => 'Y',
            'rate' => $comm_rate,
            'dr_cr' => in_array($CoverRegister->transaction_type, [self::TRANSACTION_REFUND, self::TRANSACTION_CANCEL])
                ? self::DR
                : self::CR,
            'final_amount' => $commission,
            'created_by' => Auth::user()->user_name,
            'updated_by' => Auth::user()->user_name,
        ]);
    }

    private function createSlipWording($cover_no, $endorsement_no, $type_of_bus)
    {
        $slipTemplate = SlipTemplate::where('treaty_type', $type_of_bus)->first();

        if ($slipTemplate) {
            CoverSlipWording::create([
                'cover_no' => $cover_no,
                'endorsement_no' => $endorsement_no,
                'wording' => $slipTemplate->wording,
                'created_by' => Auth::user()->user_name,
                'updated_by' => Auth::user()->user_name,
            ]);
        }
    }

    private function createCoverInstallments($CoverRegister, $request)
    {
        $no_of_installments = (int) $request->no_of_installments;

        $premiumTotals = $this->calculatePremiumTotals($CoverRegister->endorsement_no);
        $installmentAmount = $premiumTotals['totalDr'] - $premiumTotals['totalCr'];

        $baseData = [
            'cover_no' => $CoverRegister->cover_no,
            'endorsement_no' => $CoverRegister->endorsement_no,
            'layer_no' => 0,
            'trans_type' => $CoverRegister->type_of_bus,
            'entry_type' => $CoverRegister->transaction_type,
            'dr_cr' => self::DR,
            'created_by' => Auth::user()->user_name,
            'updated_by' => Auth::user()->user_name,
        ];

        if ($no_of_installments === 1) {
            CoverInstallments::create(array_merge($baseData, [
                'installment_no' => 1,
                'installment_date' => $CoverRegister->cover_from->addDays((int) $CoverRegister->premium_payment_days),
                'installment_amt' => $installmentAmount,
            ]));
        } else {
            for ($i = 0; $i < $no_of_installments; $i++) {
                CoverInstallments::create(array_merge($baseData, [
                    'installment_no' => $request->installment_no[$i],
                    'installment_date' => $request->installment_date[$i],
                    'installment_amt' => $this->parseNumeric($request->installment_amt[$i]),
                ]));
            }
        }
    }

    private function replicateReinClasses($prev_endorsement_no)
    {
        $riclasses = CoverReinclass::where('endorsement_no', $prev_endorsement_no)->get();

        $insertData = $riclasses->map(function ($ricls) {
            $data = $ricls->getAttributes();
            unset($data['id']);
            $data['endorsement_no'] = $this->_endorsement_no;
            $data['created_by'] = Auth::user()->user_name;
            $data['updated_by'] = Auth::user()->user_name;
            $data['created_at'] = now();
            $data['updated_at'] = now();
            return $data;
        })->toArray();

        if (!empty($insertData)) {
            CoverReinclass::insert($insertData);
        }
    }

    private function replicateUwClasses($prev_endorsement_no)
    {
        $uwclasses = CoverClass::where('endorsement_no', $prev_endorsement_no)->get();

        $insertData = $uwclasses->map(function ($uwcls) {
            $data = $uwcls->getAttributes();
            unset($data['id']);
            $data['endorsement_no'] = $this->_endorsement_no;
            $data['created_by'] = Auth::user()->user_name;
            $data['updated_by'] = Auth::user()->user_name;
            $data['created_at'] = now();
            $data['updated_at'] = now();
            return $data;
        })->toArray();

        if (!empty($insertData)) {
            CoverClass::insert($insertData);
        }
    }

    private function replicateRisks($prev_endorsement_no)
    {
        $risks = CoverRisk::where('endorsement_no', $prev_endorsement_no)->get();

        $insertData = $risks->map(function ($risk) {
            $data = $risk->getAttributes();
            unset($data['id']);
            $data['endorsement_no'] = $this->_endorsement_no;
            $data['created_by'] = Auth::user()->user_name;
            $data['updated_by'] = Auth::user()->user_name;
            $data['created_at'] = now();
            $data['updated_at'] = now();
            return $data;
        })->toArray();

        if (!empty($insertData)) {
            CoverRisk::insert($insertData);
        }
    }

    private function replicateAttachments($prev_endorsement_no)
    {
        $attachments = CoverAttachment::where('endorsement_no', $prev_endorsement_no)->get();

        foreach ($attachments as $attachment) {
            $data = $attachment->getAttributes();
            $id = (int) CoverAttachment::withTrashed()->max('id') + 1;
            $data['id'] = $id;
            $data['endorsement_no'] = $this->_endorsement_no;
            $data['created_by'] = Auth::user()->user_name;
            $data['updated_by'] = Auth::user()->user_name;
            $data['created_at'] = now();
            $data['updated_at'] = now();

            CoverAttachment::create($data);
        }
    }

    private function replicateReinsurers($prev_endorsement_no, $cover, $base_cover, $request, $isChangeDueDate)
    {
        $max_tran_no = (int) CoverRipart::withTrashed()->max('tran_no');

        $coverPremiums = CoverPremium::where('endorsement_no', $this->_endorsement_no)->get();

        $reinsurers = CoverRipart::where('endorsement_no', $base_cover->endorsement_no)->get();

        $currTotalRIShare = 0;

        foreach ($reinsurers as $ripart) {
            $max_tran_no++;

            $data = $this->prepareReinsurerData($ripart, $cover, $coverPremiums, $currTotalRIShare, $max_tran_no);

            CoverRipart::create($data);

            $this->generateCoverInstallment($prev_endorsement_no, $ripart, $data, $cover, $isChangeDueDate, $request);

            ReinNote::where('endorsement_no', $this->_endorsement_no)
                ->where('partner_no', $ripart->partner_no)
                ->forceDelete();

            $this->createReinsurerNotes($cover, $ripart, $data);

            $currTotalRIShare += (float) $ripart->share;
        }
    }
}
