@extends('layouts.intermediaries.base')
@section('header', 'DETAILED CALCULATOR')
@section('content')

<div class="container mt-4">
    <h3>Detailed Calculator</h3>
    
    @foreach($riskdtls as $index => $risk)
    <div class="card mb-4">
        <div class="card-header">
            <h5>{{ $risk->description }}</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center">Risk Item Details</th>
                    </tr>
                    <tr>
                        <th>Sum Insured</th>
                        <th>Rating (%)</th>
                        <th>Premium</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="sum_insured_{{ $index }}">{{ number_format($risk->sum_insured, 2) }}</td>
                        <td>
                            <input 
                                type="number" 
                                class="form-control form-control-sm rate-input" 
                                data-index="{{ $index }}" 
                                value="{{ $risk->rating }}" 
                                id="rate_{{ $index }}" 
                            />
                        </td>
                        <td id="premium-{{ $index }}">{{ number_format($risk->premium, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center">Discounts</th>
                    </tr>
                    <tr>
                        <th>Discount Name</th>
                        <th>Rate (%)</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice_extras as $i => $value)
                        @if($value->type == "discount")
                            <tr>
                                <td>{{ $value->name }}</td>
                                <td>
                                    <input 
                                        type="number" 
                                        class="form-control form-control-sm discount-input" 
                                        id="discountrate_{{ $index }}" 
                                        placeholder="Enter {{ $value->name }} Rate" 
                                        data-index="{{ $i }}" 
                                    />
                                </td>
                                <td><p id="discount_amount_{{ $i }}">0.00</p></td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center">Add-Ons</th>
                    </tr>
                    <tr>
                        <th>Add-On Name</th>
                        <th>Rate (%)</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice_extras as $j => $value)
                        @if($value->type == "addons")
                            <tr>
                                <td>{{ $value->name }}</td>
                                <td>
                                    <input 
                                        type="number" 
                                        class="form-control form-control-sm addon-input" 
                                        id="{{ $value->name.$j }}" 
                                        placeholder="Enter {{ $value->name }} Rate" 
                                        data-index="{{ $j }}" 
                                    />
                                </td>
                                <td><p id="addon_amount_{{ $j }}">0.00</p></td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach



    <!-- Total Premium -->
    <div class="mt-4">
        <h5>Total Premium</h5>
        <p id="total_premium">Total Premium: <strong>{{ number_format($riskdtls->sum('premium'), 2) }}</strong></p>
        <p id="total_premium_with_levies">Total Premium with Levies: <strong>{{ number_format($riskdtls->sum('premium') * 1.004625, 2) }}</strong></p>
    </div>
</div>

@endsection

@section('page_scripts')
<script>
    $(document).ready(function () {
        function recalculatePremium(index) {
            var sumInsured = parseFloat($('#sum_insured_' + index).text().replace(/,/g, ''));
            var rate = parseFloat($('#rate_' + index).val());
            console.log(rate,sumInsured);
          
            // var ltaDiscount = parseFloat($('#lta_discount_' + index).val()) || 0;
            // var noClaimDiscount = parseFloat($('#no_claim_discount_' + index).val()) || 0;
            // var electricalAddon = parseFloat($('#electrical_addon_' + index).val()) || 0;
            // var debrisRemoval = parseFloat($('#debris_removal_' + index).val()) || 0;
            // var spontaneousCombustion = parseFloat($('#spontaneous_combustion_' + index).val()) || 0;

            // Calculate the premium based on rate
            var premium = sumInsured * (rate / 100);

            // Calculate discounts
            // var ltaDiscountPremium = (100 - ltaDiscount)/100 * premium;
            // var noClaimDiscountPremium = (100 - noClaimDiscount)/100 * premium;

            // // Calculate add-ons
            // var electricalAddonPremium = (electricalAddon / 100) * sumInsured;
            // var debrisRemovalPremium = (debrisRemoval / 100) * sumInsured;
            // var spontaneousCombustionPremium = (spontaneousCombustion / 100) * sumInsured;

            // // Apply discounts and add-ons
            // var discountFactor = (1 - (ltaDiscount / 100)) * (1 - (noClaimDiscount / 100));
            // var addonFactor = (1 + (electricalAddon / 100)) * (1 + (debrisRemoval / 100)) * (1 + (spontaneousCombustion / 100));

            // Final premium calculation
            // var finalPremium = premium - ltaDiscountPremium - noClaimDiscountPremium + electricalAddonPremium + debrisRemovalPremium + spontaneousCombustionPremium;
            var finalPremium = premium

            // Update premiums
            $('#premium-' + index).text(finalPremium.toFixed(2));
            // $('#lta_discount_premium_' + index).text(ltaDiscountPremium.toFixed(2));
            // $('#no_claim_discount_premium_' + index).text(noClaimDiscountPremium.toFixed(2));
            // $('#electrical_addon_premium_' + index).text(electricalAddonPremium.toFixed(2));
            // $('#debris_removal_premium_' + index).text(debrisRemovalPremium.toFixed(2));
            // $('#spontaneous_combustion_premium_' + index).text(spontaneousCombustionPremium.toFixed(2));

            // Recalculate total premium with levies
            var totalPremium = 0;
            $('.premium-cell').each(function () {
                totalPremium += parseFloat($(this).text()) || 0;
            });

            var totalPremiumWithLevies = totalPremium * 1.004625;

            $('#total_premium').text('Total Premium: ' + totalPremium.toFixed(2));
            $('#total_premium_with_levies').text('Total Premium with Levies: ' + totalPremiumWithLevies.toFixed(2));
        }
        function recalculateDiscount(index) {
            // var sumInsured = parseFloat($('#sum_insured_' + index).text().replace(/,/g, ''));
            // var rate = parseFloat($('#rate_' + index).val());
            // console.log(rate,sumInsured);
          
            // var ltaDiscount = parseFloat($('#lta_discount_' + index).val()) || 0;
            // var noClaimDiscount = parseFloat($('#no_claim_discount_' + index).val()) || 0;
            // var electricalAddon = parseFloat($('#electrical_addon_' + index).val()) || 0;
            // var debrisRemoval = parseFloat($('#debris_removal_' + index).val()) || 0;
            // var spontaneousCombustion = parseFloat($('#spontaneous_combustion_' + index).val()) || 0;

            // Calculate the premium based on rate
            // var premium = sumInsured * (rate / 100);

            // Calculate discounts
            // var ltaDiscountPremium = (100 - ltaDiscount)/100 * premium;
            // var noClaimDiscountPremium = (100 - noClaimDiscount)/100 * premium;

            // // Calculate add-ons
            // var electricalAddonPremium = (electricalAddon / 100) * sumInsured;
            // var debrisRemovalPremium = (debrisRemoval / 100) * sumInsured;
            // var spontaneousCombustionPremium = (spontaneousCombustion / 100) * sumInsured;

            // // Apply discounts and add-ons
            // var discountFactor = (1 - (ltaDiscount / 100)) * (1 - (noClaimDiscount / 100));
            // var addonFactor = (1 + (electricalAddon / 100)) * (1 + (debrisRemoval / 100)) * (1 + (spontaneousCombustion / 100));

            // Final premium calculation
            // var finalPremium = premium - ltaDiscountPremium - noClaimDiscountPremium + electricalAddonPremium + debrisRemovalPremium + spontaneousCombustionPremium;
           // var finalPremium = premium

            // Update premiums
            //$('#premium-' + index).text(finalPremium.toFixed(2));
            // $('#lta_discount_premium_' + index).text(ltaDiscountPremium.toFixed(2));
            // $('#no_claim_discount_premium_' + index).text(noClaimDiscountPremium.toFixed(2));
            // $('#electrical_addon_premium_' + index).text(electricalAddonPremium.toFixed(2));
            // $('#debris_removal_premium_' + index).text(debrisRemovalPremium.toFixed(2));
            // $('#spontaneous_combustion_premium_' + index).text(spontaneousCombustionPremium.toFixed(2));

            // Recalculate total premium with levies
            // var totalPremium = 0;
            // $('.premium-cell').each(function () {
            //     totalPremium += parseFloat($(this).text()) || 0;
            // });

            // var totalPremiumWithLevies = totalPremium * 1.004625;

            // $('#total_premium').text('Total Premium: ' + totalPremium.toFixed(2));
            // $('#total_premium_with_levies').text('Total Premium with Levies: ' + totalPremiumWithLevies.toFixed(2));
        }

        // Recalculate premium on input changes
        $('input[id^="rate_"]').on('input', function () {
            var index = $(this).data('index');
           
            recalculatePremium(index);
        });
        //Discount
        $('input[id^="discountrate_"]').on('input', function () {
            var index = $(this).data('index');
           
            recalculateDiscount(index);
        });
    });
</script>
@endsection
