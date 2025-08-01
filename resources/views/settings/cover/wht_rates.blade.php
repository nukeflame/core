@extends('layouts.app')

@section('content')
<nav class="page-title fw-semibold fs-18 mb-0 bg-white mt-2 mb-2 p-1" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Settings</a></li>
        <li class="breadcrumb-item">Cover</li>
        <li class="breadcrumb-item active" aria-current="page">Witholding Taxes</li>
    </ol>

</nav>
@include('settings.settings_menu')

<div class="container card">
    <button class="btn btn-outline-primary col-md-2 btn-sm mt-3" id="addWht" data-bs-toggle="modal" data-bs-target="#whtModal">
        <i class="fa fa-plus"></i> New WHT
    </button>
    <table class="table" id="wht-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Rate (%)</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- Start of modal --}}
<div class="modal fade" id="whtModal" tabindex="-1" aria-labelledby="process" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="whtForm" action="{{ route('settings.saveWhtRate') }}" method="post">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-header bg-primary">
                    <h6 class="modal-title text-white" id="staticBackdropLabel1">New Witholding Tax</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-4">
                        <div class="col-sm-8 mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" placeholder="Wht Description" aria-label="Wht Description" id="description" name="description" required>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label class="form-label">Rate</label>
                            <input type="number"  class="form-control" placeholder="Rate" aria-label="Rate" id="rate" name="rate" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-outline-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- End of modal --}}

@endsection

@push('script')
<script>
    $(document).ready(function() {
        const processTable = $('#wht-table').DataTable({
            processing: true,
            serverSide: true,
            bAutoWidth: false,
            ajax: {
                url: "{!! route('settings.whtRate') !!}",
            },
            columns: [
                {data: 'id'},
                {data: 'description'},
                {data: 'rate'},
                {data: 'created_by'},
                {data: 'updated_by'},
                {data: 'action'},
            ]
        });

        $('#whtModal').on('shown.bs.modal', function() {
            $('.form-select').select2({
                dropdownParent: $('#whtModal')
            });
        })


        $('#wht-table').on('click','.edit',function(){
            const data = $(this).data('data')

            const url = "{!! route('settings.editWhtRate') !!}"
            $("#whtForm").attr('action',url)
            $("#id").val(data.id)
            $("#description").val(data.description)
            $("#rate").val(data.rate)
            $("#staticBackdropLabel1").text("Edit Witholding Tax")
        })

        $("#addWht").click(function (e) { 
            e.preventDefault();
            const url = "{!! route('settings.saveWhtRate') !!}"
            $("#whtForm").attr('action',url)
            $("#whtForm")[0].reset()
            $("#staticBackdropLabel1").text("Add Witholding Tax")
        });

        $("#whtForm").validate({
            errorClass: "errorClass",
            rules: {
                description: {
                    required: true
                },
                rate: {
                    required: true
                },
            },
            // submitHandler: function(form) {
            //     $('#whtForm').submit()
            // }
        })

        $(document).on('click','.delete',function(){
            const shareData = $(this).data('data');
            swal.fire({
                title: 'Remove Witholding Tax',
                text:`This action will remove the Item ${shareData.description} `,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Remove',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                const data = {
                    id : shareData.id,
                }
                if(result.isDismissed)
                {
                    return false;
                }
                // subit commit request
                const url = "{!! route('settings.deleteWhtRate') !!}"
                fetchWithCsrf(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type':'application/json'
                    },
                    body: JSON.stringify(data) ,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status == 200) {
                        toastr.success("Action was successful",'Successful')
                        setTimeout(() => {
                                location.reload();
                            }, 3000);
                    } 
                    else if (data.status == 422) {
                        // Validation errors
                        showServerSideValidationErrors(data.errors)

                    }
                    else {
                        toastr.error("Failed to delete item")
                    }
                })
                .catch(error => {
                    console.log(error);
                    toastr.error("An internal error occured")
                });
            });
        })
    });
</script>
@endpush