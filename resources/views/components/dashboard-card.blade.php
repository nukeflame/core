@props(['data','cardType'])
<div class="col-lg-3 col-sm-3 col-md-3 col-xl-3">
    <div class="card custom-card hrm-main-card  @if(isset($cardType)){{ $cardType }} @endif">
        <div class="card-body">
            <div class="row">
                <div
                    class="col-xxl-3 col-xl-2 col-lg-3 col-md-3 col-sm-4 col-4 d-flex align-items-center justify-content-center ecommerce-icon px-0">
                    <span class="rounded p-2 bg-primary-transparent">
                        <span class="avatar bg-danger"> <i class="fa-solid fa-layer-group"></i></span>
                    </span>
                </div>
                <div class="col-xxl-9 col-xl-10 col-lg-9 col-md-9 col-sm-8 col-8 px-0">
                    <div class="fw-semibold text-muted d-block mb-2">{{ $data['title'] }}</div>
                    <div class="text-muted mb-1 fs-12">
                        <span class="text-dark fw-semibold fs-20 lh-1 vertical-bottom">
                            {{ $data['amount'] }}
                        </span>
                    </div>
                    <div>
                        @if($data['diffcount'] > 0)
                        <span class="fs-12 mb-0">Increase by <span
                                class="badge bg-success-transparent text-success mx-1">+{{ $data['diffcount'] }}%</span> this
                            month compared to last month</span>
                            @else
                            <span class="fs-12 mb-0">Decrease by <span
                                class="badge bg-danger-transparent text-danger mx-1">{{ $data['diffcount'] }}%</span> this
                            month compared to last month</span>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>