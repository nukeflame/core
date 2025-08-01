@extends('layouts.app', [
    'pageTitle' => 'Staff Notice - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Staff Notice</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.staff_notices') }}">Staff Notice</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Notices
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">Staff Notices</div>
                    <button id="addNoticeBtn" class="btn btn-success btn-sm">
                        <i class="bx bx-plus"></i> Add Staff Notice
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="noticesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Notice</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Effective From</th>
                                    <th>Expired At</th>
                                    <th>Issued By</th>
                                    <th>Priority</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="noticeModal" tabindex="-1" aria-labelledby="noticeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title dc-modal-title" id="noticeModalLabel"><i class="bx bx-pin me-1"></i> Add Store
                        Notice</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="noticeForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" name="notice_id" id="notice_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="notice" class="form-label">Notice Title</label>
                            <input type="text" class="form-inputs" id="notice" name="notice" required>
                            <div class="invalid-feedback" id="notice-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-inputs" id="description" name="description" rows="5"></textarea>
                            <div class="invalid-feedback" id="description-error"></div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-inputs select2" id="type" name="type">
                                    <option value="all_users">All users</option>
                                    <option value="selected_user">Selected user</option>
                                    <option value="user">User</option>
                                </select>
                                <div class="invalid-feedback" id="type-error"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="issued_by" class="form-label">Issued By</label>
                                <input type="text" class="form-inputs" id="issued_by" name="issued_by" required>
                                <div class="invalid-feedback" id="issued_by-error"></div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="effective_from" class="form-label">Effective From</label>
                                <input type="datetime-local" class="form-inputs" id="effective_from" name="effective_from"
                                    required>
                                <div class="invalid-feedback" id="effective_from-error"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expired_at" class="form-label">Expired At</label>
                                <input type="datetime-local" class="form-inputs" id="expired_at" name="expired_at">
                                <div class="invalid-feedback" id="expired_at-error"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-inputs select2" id="priority" name="priority">
                                <option value="LOW">Low</option>
                                <option value="MEDIUM" selected>Medium</option>
                                <option value="HIGH">High</option>
                            </select>
                            <div class="invalid-feedback" id="priority-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark" id="saveNotice"><i class="bi bi-save me-2"></i> Save
                            Notice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            let $table = $('#noticesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.store-notices.getData') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        class: 'highlight-idx'
                    },
                    {
                        data: 'notice',
                        name: 'notice'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        class: 'highlight-description'

                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'effective_from',
                        name: 'effective_from'
                    },
                    {
                        data: 'expired_at',
                        name: 'expired_at'
                    },
                    {
                        data: 'issued_by',
                        name: 'issued_by'
                    },
                    {
                        data: 'priority_badge',
                        name: 'priority'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#addNoticeBtn').click(function(e) {
                e.preventDefault();
                resetForm();
                $('#noticeModalLabel').html('<i class="bx bx-pin me-1"></i> Add Store Notice');
                $('#method').val('POST');
                $('#noticeModal').modal('show');
            });

            $('#noticeForm').submit(function(e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const formData = $(this).serialize();
                const noticeId = $('#notice_id').val();
                const method = $('#method').val();
                let url = "{{ route('admin.staff-notices.store') }}";
                let type = "POST";

                if (method === 'PUT') {
                    url = "{{ url('store-notices') }}/" + noticeId;
                    type = "PUT";
                }

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    success: function(response) {
                        $('#noticeModal').modal('hide');
                        $table.ajax.reload();
                        toastr.success(response.message ? response.message :
                            'Notice saved successfully.');

                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]);
                            });
                        } else {
                            toastr.error('An error occurred. Please try again.');
                        }
                    }
                });
            });

            $(document).on('click', '.edit', function() {
                resetForm();
                const noticeId = $(this).data('id');

                // $.ajax({
                //     url: "{{ url('staff-notices') }}/" + noticeId + "/edit",
                //     type: 'GET',
                //     success: function(response) {
                //         $('#noticeModalLabel').text('Edit Store Notice');
                //         $('#notice_id').val(response.id);
                //         $('#notice').val(response.notice);
                //         $('#description').val(response.description);
                //         $('#type').val(response.type);
                //         $('#issued_by').val(response.issued_by);

                //         if (response.effective_from) {
                //             const effectiveDate = new Date(response.effective_from);
                //             $('#effective_from').val(formatDateTime(effectiveDate));
                //         }

                //         if (response.expired_at) {
                //             const expiredDate = new Date(response.expired_at);
                //             $('#expired_at').val(formatDateTime(expiredDate));
                //         }

                //         $('#priority').val(response.priority);
                //         $('#method').val('PUT');
                //         $('#noticeModal').modal('show');
                //     },
                //     error: function() {
                //         alert('Error fetching notice data');
                //     }
                // });
            });

            var noticeId;

            $(document).on('click', '.delete', function() {
                // noticeId = $(this).data('id');
                // $('#deleteModal').modal('show');
            });

            $('#confirmDelete').click(function() {
                $.ajax({
                    url: "{{ url('staff-notices') }}/" + noticeId,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();

                        const successAlert = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                        $('.container').prepend(successAlert);

                        setTimeout(function() {
                            $('.alert').alert('close');
                        }, 5000);
                    },
                    error: function() {
                        alert('Error deleting notice');
                    }
                });
            });

            function resetForm() {
                $('#noticeForm')[0].reset();
                $('#method').val('POST');
                $('#notice_id').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            function formatDateTime(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');

                return `${year}-${month}-${day}T${hours}:${minutes}`;
            }

        })
    </script>
@endpush
