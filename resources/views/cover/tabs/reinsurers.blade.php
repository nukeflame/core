<div class="card">
    <div class="card-body py-3 px-2">
        <table id="reinsurers-table" class="table table-striped text-nowrap table-hover table-responsive"
            data-url="{{ route('cover.reinsurers_datatable') }}"
            data-delete-url="{{ route('cover.delete_reinsurance_data') }}" style="width: 100%!important;">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Reinsurer</th>
                    <th scope="col">Share(%)</th>
                    @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
                        <th scope="col">Sum insured</th>
                        <th scope="col">Premium</th>
                        <th scope="col">Commission rate</th>
                        <th scope="col">Commission</th>
                        <th scope="col">Brokerage Commission</th>
                        <th scope="col">WHT Amount</th>
                        <th scope="col">Retro Amount</th>
                    @endif
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- @props(['cover'])

<div class="card">
    <div class="card-body py-3 px-2">
        <table id="reinsurers-table" class="table table-striped text-nowrap table-hover table-responsive"
            style="width: 100%!important;">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Reinsurer</th>
                    <th scope="col">Share(%)</th>
                    @switch ($cover->type_of_bus)
                        @case('FPR')
                        @case('FNP')
                            <th scope="col">Sum insured</th>
                            <th scope="col">Premium</th>
                            <th scope="col">Commission rate</th>
                            <th scope="col">Commission</th>
                            <th scope="col">Brokerage Commission</th>
                            <th scope="col">WHT Amount</th>
                            <th scope="col">Retro Amount</th>
                        @break

                        @case('TPR')
                            @if (!in_array($cover->transaction_type, ['NEW', 'REN']))
                                {{-- <th scope="col">Sum insured</th> -
                                <th scope="col">Premium</th>
                                <th scope="col">Commission</th>
                                <th scope="col">Claim Amt</th>
                                <th scope="col">Premium Tax</th>
                                <th scope="col">Reinsurance Tax</th>
                            @endif
                        @break

                        @case('TNP')
                            @if (!in_array($cover->transaction_type, ['NEW', 'REN']))
                                <th scope="col">Total MDP</th>
                                <th scope="col">MDP</th>
                            @endif
                        @break

                    @endswitch
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div> --}}
