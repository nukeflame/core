@extends('layouts.app')

@section('content')
<nav class="page-title fw-semibold fs-18 mb-0 bg-white mt-2 mb-2 p-1" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Settings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Settings Menus</li>
    </ol>

</nav>
@include('settings.settings_menu')

<div class="container card">
    <button class="btn btn-outline-primary col-md-2 btn-sm mt-3" id="addDoc" data-bs-toggle="modal" data-bs-target="#menuModal">
        <i class="fa fa-plus"></i> New Menu
    </button>
    <table class="table" id="menu-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Route</th>
                <th>Parent</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

{{-- Start of modal --}}
<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="process" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <form id="ackDocsForm" action="{{ route('settings.saveSettingsMenus') }}" method="post">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-header bg-primary">
                    <h6 class="modal-title text-white" id="staticBackdropLabel1">New Menu</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-4">
                        <div class="col-sm-4 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" placeholder="title" aria-label="title" id="title" name="title" required>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label class="form-label">Route</label>
                            <select name="route" id="route" class="form-select select2">
                                <option value="">--Select Route--</option>
                                <option value="">Root (#)</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->getName()}}">{{ $route->getName()}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label class="form-label">Parent</label>
                            <select name="parent_id" id="parent_id" class="form-select select2">
                                <option value="">--Select Parent--</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id}}">{{ $menu->title }}</option>
                                @endforeach
                            </select>
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
        const processTable = $('#menu-table').DataTable({
            processing: true,
            serverSide: true,
            bAutoWidth: false,
            ajax: {
                url: "{!! route('settings.settingsMenus') !!}",
            },
            columns: [
                {data: 'id'},
                {data: 'title'},
                {data: 'route'},
                {data: 'parent'},
                {data: 'action'},
            ]
        });

        $('#menuModal').on('shown.bs.modal', function() {
            $('.form-select').select2({
                dropdownParent: $('#menuModal')
            });
        })


        $('#menu-table').on('click','.edit',function(){
            const data = $(this).data('data')

            const url = "{!! route('settings.editSettingsMenus') !!}"
            $("#ackDocsForm").attr('action',url)
            $("#id").val(data.id)
            $("#title").val(data.title)
            $("#parent_id").val(data.parent_id).select2()
            $("#route").val(data.route).select2()
            $("#staticBackdropLabel1").text("Edit menu")
        })

        $("#addDoc").click(function (e) { 
            e.preventDefault();
            const url = "{!! route('settings.saveSettingsMenus') !!}"
            $("#ackDocsForm").attr('action',url)
            $("#ackDocsForm")[0].reset()
            $("#staticBackdropLabel1").text("Add menu")
        });

        $("#ackDocsForm").validate({
            errorClass: "errorClass",
            rules: {
                title: {
                    required: true
                },
                rate: {
                    required: true
                },
            },
            // submitHandler: function(form) {
            //     $('#ackDocsForm').submit()
            // }
        })

                    
        $(document).on('click','.delete',function(){
            const shareData = $(this).data('data');
            swal.fire({
                title: 'Remove Menu',
                text:`This action will remove the menu ${shareData.title}`,
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
                const url = "{!! route('settings.deleteSettingsMenus') !!}"
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