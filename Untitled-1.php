        //   $query = CoverRipart::where([
        //         'cover_no' => $coverNo,
        //         'endorsement_no' => $endorsementNo
        //     ])->with('partner');

        //     $debit = DebitNote::where([
        //         'cover_no' => $coverNo,
        //         'endorsement_no' => $endorsementNo
        //     ])->first();

        //     $recordsTotal = $query->count();
        //     $recordsFiltered = $recordsTotal;

        //     $reinsurers = $query
        //         ->skip($start)
        //         ->take($length)
        //         ->get();

        //     $data = $reinsurers->map(function ($rein) {
        //         return [
        //             'id' => $rein->id,
        //             'name' => $rein->partner?->name ?? '',
        //             'share_percentage' => $rein->share ?? 0,
        //             'gross_premium' => $rein->total_premium ?? 0,
        //             'commission' => $rein->commission ?? 0,
        //             'brokerage_amount' => $rein->brokerage_comm_amt ?? 0,
        //             'premium_tax_amount' => $rein->prem_tax ?? 0,
        //             'wht_amount' => $rein->wht_amt ?? 0,
        //             'ri_tax' => $rein->ri_tax ?? 0,
        //             'net_amount' => $rein->net_amount ?? 0,
        //             'status' => 'active'
        //         ];

