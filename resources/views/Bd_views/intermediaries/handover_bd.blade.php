{{-- @extends('layouts.admincast') --}}
@extends('layouts.intermediaries.base')
{{-- @extends('layouts.intermediaries.base') --}}
@section('header','MENU ITEMS')
@section('content')



<div class="m-2">
    

        <div class="card table-responsive">
            <div class="card-body">
                <table class="table table-striped table-hover" id="client-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Year</th>
                            <th>Industry</th>
                            <th>Lead Owner</th>
                            <th>Action</th>
                            
                        </tr>
                    </thead>
        
                </table>
            </div>
        </div>
      
</div>
@endsection
@section('page_scripts')

<script>
    $(document).ready(function () {
        $("#myInput").on("change", function () {
            var value = $(this).val().toLowerCase();

            $("#client-table > tbody > tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
        $('#client-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('leads.get')}}",
                type: "get",
                data: function(d) {
                        d.handover = 'Y';
                    }
                            
            },
            columns: [
                {data:'fullname',name:'fullname'},   
                {data:'pip_year',name:'pip_year'},
                {data:'industry',name:'industry'},
                {data:'lead_owner',name:'lead_owner'},
                {data:'action',name:'action'},
                ]		
        });

        
        $('body').on('click', 'table .add_to_pipeline', function(){
            console.log("kojihugyft");
            let prospect = $(this).attr('data-prospect')
            Swal.fire({
                    title: "Warning!",
                    html: "Are You Sure You Want to add this prospect to Sales Management",
                    icon: "warning",
                    confirmButtonText: "Yes",
                    showCancelButton: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'GET',
                            data:{'prospect':prospect},
                            url: "{!! route('prospect.add.pipeline')!!}",
                            success: function(response) {
                                if (response.status == 1) {
                                    toastr.success(response.message, {
                                            timeOut: 5000
                                        });
                                }
                                    $('#client-table').DataTable().ajax.reload();
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                title: "Error",
                                text: textStatus,
                                icon: "error"
                                });
                            }
                        });
                    }
                })
        })

    })
</script>

@endsection
