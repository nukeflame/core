<?php

namespace Nukeflame\Core\Services;

use App\Models\CoverPremium;
use App\Models\CoverRegister;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StorePropPortfolioService
{
    /**
     * @param  callable(string,string):object|array  $generateEndorseNo
     */
    public function execute(
        Request $request,
        callable $generateEndorseNo,
        int $currentYear,
        int $accountYear,
        int $accountMonth,
        string $username
    ): array {
        $validated = $this->validate($request, $currentYear);

        $typeOfBus = (string) $validated['type_of_bus'];
        $transType = 'POT';
        $previousEndorsementNo = (string) ($validated['orig_endorsement'] ?? $validated['endorsement_no']);

        $prevCover = CoverRegister::where('endorsement_no', $previousEndorsementNo)->lockForUpdate()->first();
        if (! $prevCover) {
            throw ValidationException::withMessages([
                'orig_endorsement' => ['The selected endorsement reference does not exist.'],
            ]);
        }

        $endorsement = $generateEndorseNo($typeOfBus, $transType);
        $newEndorsementNo = (string) ($endorsement->endorsement_no ?? $endorsement['endorsement_no'] ?? '');
        $coverSerialNo = (string) ($endorsement->serial_no ?? $endorsement['serial_no'] ?? '');

        if ($newEndorsementNo === '') {
            throw ValidationException::withMessages([
                'type_of_bus' => ['Unable to generate endorsement number.'],
            ]);
        }

        $portfolioType = (string) $validated['portfolio_type'];

        $premiumRate = $this->parseNumber($validated['port_prem_rate']);
        $lossRate = $this->parseNumber($validated['port_loss_rate']);
        $premiumBase = $this->parseNumber($validated['port_premium_amt']);
        $lossBase = $this->parseNumber($validated['port_outstanding_loss_amt']);

        $portPremAmt = ($premiumRate / 100) * $premiumBase;
        $portLossAmt = ($lossRate / 100) * $lossBase;

        $premTaxRate = (float) ($prevCover->prem_tax_rate ?? 0);
        $riTaxRate = (float) ($prevCover->ri_tax_rate ?? 0);
        $profitCommRate = (float) ($prevCover->profit_comm_rate ?? 0);

        if ($portfolioType === 'IN') {
            $premiumDesc = 'Portfolio Entry Premium';
            $lossDesc = 'Portfolio Entry Loss';
            $premiumEntryType = 'PEP';
            $lossEntryType = 'PEL';
            $premiumOrder = 6;
            $lossOrder = 7;
            $premiumDrCr = 'DR';
            $lossDrCr = 'DR';
            $coverTitle = 'PORTFOLIO IN STATEMENT';
        } else {
            $premiumDesc = 'Portfolio Withdrawal Premium';
            $lossDesc = 'Portfolio Withdrawal Loss';
            $premiumEntryType = 'PWP';
            $lossEntryType = 'PWL';
            $premiumOrder = 8;
            $lossOrder = 9;
            $premiumDrCr = 'CR';
            $lossDrCr = 'CR';
            $coverTitle = 'PORTFOLIO OUT STATEMENT';
        }

        // $cover = $prevCover->replicate(['id']);
        // if ($coverSerialNo !== '') {
        //     $cover->cover_serial_no = $coverSerialNo;
        // }
        // $cover->endorsement_no = $newEndorsementNo;
        // $cover->orig_endorsement_no = $prevCover->orig_endorsement_no ?: $previousEndorsementNo;
        // $cover->transaction_type = $transType;
        // $cover->cover_title = $coverTitle;
        // $cover->currency_code = $validated['currency_code'];
        // $cover->currency_rate = $this->parseNumber($validated['today_currency']);
        // $cover->verified = 'A';
        // $cover->status = 'A';
        // $cover->commited = 'Y';
        // $cover->account_year = $accountYear;
        // $cover->account_month = $accountMonth;
        // $cover->dola = Carbon::now();
        // $cover->created_by = $username;
        // $cover->updated_by = $username;
        // $cover->save();

        $postingQuarter = Carbon::parse($validated['posting_date'])->quarter;
        $postingYear = Carbon::parse($validated['posting_year'])->year;

        // if ($portPremAmt > 0) {
        //     $this->storePremiumLine(
        //         $cover,
        //         $previousEndorsementNo,
        //         $transType,
        //         $premiumDesc,
        //         $premiumEntryType,
        //         $premiumOrder,
        //         $premiumDrCr,
        //         $portPremAmt,
        //         $premiumRate,
        //         $postingQuarter,
        //         $username
        //     );
        // }

        // if ($portLossAmt > 0) {
        //     $this->storePremiumLine(
        //         $cover,
        //         $previousEndorsementNo,
        //         $transType,
        //         $lossDesc,
        //         $lossEntryType,
        //         $lossOrder,
        //         $lossDrCr,
        //         $portLossAmt,
        //         $lossRate,
        //         $postingQuarter,
        //         $username
        //     );
        // }

        // logger()->debug(json_encode($ff, JSON_PRETTY_PRINT));


        return [
            'validatedData' => $validated,
            'newEndorsementNo' => $newEndorsementNo,
            'cover' => $prevCover,
            'prevCover' => $prevCover,
            'computed' => [
                'premium_desc' => $premiumDesc,
                'loss_desc' => $lossDesc,
                'port_prem_amt' => $portPremAmt,
                'port_loss_amt' => $portLossAmt,
                'prem_tax_rate' => $premTaxRate,
                'ri_tax_rate' => $riTaxRate,
                'profit_comm_rate' => $profitCommRate,
            ],
            'premiumEntryType' => $premiumEntryType,
            'lossEntryType' => $lossEntryType,
            'premiumOrder' => $premiumOrder,
            'premiumDrCr' => $premiumDrCr,
            'premiumDrCr' => $premiumDrCr,
            'lossDrCr' => $lossDrCr,
            'coverTitle' => $coverTitle,
            'postingQuarter' => $postingQuarter,
            'postingYear' => $postingYear
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function validate(Request $request, int $currentYear): array
    {
        $validator = Validator::make(
            $request->all(),
            [
                'cover_no' => ['required', 'string'],
                'type_of_bus' => ['required', 'string'],
                'orig_endorsement' => ['required', 'string', 'exists:cover_register,endorsement_no'],
                'posting_year' => ['required', 'integer', 'min:' . ($currentYear - 2), 'max:' . ($currentYear + 1)],
                'posting_date' => ['required', 'date'],
                'currency_code' => ['required', 'string', 'max:10'],
                'today_currency' => ['required', 'numeric', 'gt:0'],
                'portfolio_type' => ['required', 'in:IN,OUT'],
                'port_prem_rate' => ['required', 'numeric', 'min:0', 'max:100'],
                'port_premium_amt' => ['required'],
                'port_loss_rate' => ['required', 'numeric', 'min:0', 'max:100'],
                'port_outstanding_loss_amt' => ['required'],
                'comments' => ['nullable', 'string', 'max:2000'],
                'show_cedant' => ['nullable', 'boolean'],
                'show_reinsurer' => ['nullable', 'boolean'],
            ],
            [
                'orig_endorsement.required' => 'Cover reference is required.',
                'orig_endorsement.exists' => 'The selected cover reference is invalid.',
            ]
        );

        $validator->after(function ($validator) use ($request) {
            $postingDate = $request->input('posting_date');
            $postingYear = (int) $request->input('posting_year');

            if ($postingDate) {
                try {
                    $parsedDate = Carbon::parse((string) $postingDate);
                    if ((int) $parsedDate->year !== $postingYear) {
                        $validator->errors()->add('posting_date', 'Posting date year must match underwriting year.');
                    }
                } catch (\Throwable) {
                    $validator->errors()->add('posting_date', 'Posting date is invalid.');
                }
            }

            foreach (['port_premium_amt', 'port_outstanding_loss_amt'] as $field) {
                $value = $request->input($field);
                if ($value === null || $value === '') {
                    continue;
                }

                if ($this->parseNumber($value) < 0) {
                    $validator->errors()->add($field, 'Amount must be zero or greater.');
                }
            }
        });

        return $validator->validate();
    }

    private function storePremiumLine(
        CoverRegister $cover,
        string $prevEndorsementNo,
        string $transType,
        string $description,
        string $entryType,
        int $order,
        string $drCr,
        float $amount,
        float $rate,
        int $quarter,
        string $username
    ): void {
        CoverPremium::create([
            'cover_no' => $cover->cover_no,
            'endorsement_no' => $cover->endorsement_no,
            'orig_endorsement_no' => $prevEndorsementNo,
            'transaction_type' => $transType,
            'premium_type_code' => 0,
            'premtype_name' => $description,
            'quarter' => $quarter,
            'entry_type_descr' => $entryType,
            'premium_type_order_position' => $order,
            'premium_type_description' => $description,
            'type_of_bus' => $cover->type_of_bus,
            'class_code' => $cover->class_code ?? 'ALL',
            'treaty' => $cover->treaty_type ?? 'SURP',
            'basic_amount' => $amount,
            'apply_rate_flag' => 'N',
            'rate' => $rate,
            'dr_cr' => $drCr,
            'final_amount' => $amount,
            'created_by' => $username,
            'updated_by' => $username,
        ]);
    }

    private function parseNumber(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return (float) str_replace(',', '', (string) $value);
    }
}
