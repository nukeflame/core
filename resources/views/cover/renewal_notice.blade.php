@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Renewal Notice</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href>Renewal Notice</a></li>
                    <li class="breadcrumb-item"><a href>Cover No</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $cover_no }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row">
        <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-1 m-2" id="generateNotice"><i
                class="bx bx-analyse"></i>
            Generate</button>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Renewals</div>
                </div>
                <div class="card-body">
                    {{ html()->form('POST', '/customer/customer-dtl')->id('form_customer_datatable')->open() }}
                    <input type="text" id="customer_id" name="customer_id" hidden />
                    <table class="table text-nowrap table-striped table-hover" id="renewal-table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Renewal Type</th>
                                <th scope="col">Expires</th>
                                <th scope="col">Renewal Notice</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                    </table>
                    {{ csrf_field() }}
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Document attachement Modal  -->
    <div class="modal effect-scale md-wrapper" id="view-renewaldocument-modal" tabindex="-1" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true" aria-labelledby="staticRenewalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticRenewalLabel" style="font-size: 19px; vertical-align: -3px;"><i
                            class="bx bx-envelope"></i> Send Renewal Notice
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="renewalEmailForm" action="{{ route('cover.sendrenewal.email') }}" method="POST">
                        @csrf
                        <input type="hidden" name="recipentType" id="recipentType" />
                        <input type="hidden" name="policyId" id="policyId" />
                        <div class="row">
                            {{-- <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="templateSelect" class="form-label">Email Template</label>
                                    <select class="form-inputs select2" id="emailTemplateSelect" disabled>
                                        <option value="">Select a template...</option>
                                        <option value="1">Standard Renewal Notice</option>
                                        <option value="2">Urgent Renewal Notice</option>
                                        <option value="3">Final Renewal Notice</option>
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="emailTo" class="form-label">Email To</label>
                                    <select class="form-inputs select2" id="emailTo" multiple name="emailTo" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="emailSubject" class="form-label">Subject</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-chat-square-text"></i>
                                        </span>
                                        <input type="text" class="form-control color-blk" name="emailSubject"
                                            id="emailSubject" placeholder="Enter email subject" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="emailContent" class="form-label">Content</label>
                                    <div class="card">
                                        <div class="card-header bg-white p-0">
                                            {{-- <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-light border-radius-0"><i
                                                        class="bi bi-type-bold"></i></button>
                                                <button type="button" class="btn btn-light"><i
                                                        class="bi bi-type-italic"></i></button>
                                                <button type="button" class="btn btn-light"><i
                                                        class="bi bi-list-ul"></i></button>
                                                <button type="button" class="btn btn-light"><i
                                                        class="bi bi-link"></i></button>
                                            </div> --}}
                                        </div>
                                        <div class="card-body p-0">
                                            <textarea class="form-control form-control-sm resize-none color-blk" id="renewal-email" rows="15"
                                                placeholder="Enter email content" name="emailContent" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mails-information mb-2">
                                    <div class="mail-attachments">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="mb-0">
                                                <span class="fs-14 fw-semibold"><i
                                                        class="ri-attachment-2 me-1 align-middle"></i>Attachments <span
                                                        id="attachment_status"></span></span>
                                            </div>
                                            <div>
                                            </div>
                                        </div>
                                        <div class="mt-2 d-flex" id="attachments-container">

                                            <a href="javascript:void(0);"
                                                class="mail-attachement-sendbtn btn btn-icon btn-outline-light ms-2 btn-lg border"
                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Upload">
                                                <i class="ri-attachment-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                    {{-- <label class="form-label">Attachments</label>
                                    <div class="attachment-box">
                                        <label
                                            class="d-flex align-items-center justify-content-center gap-2 cursor-pointer text-dark">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="text-secondary">
                                                <path
                                                    d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48">
                                                </path>
                                            </svg>
                                            <span class="text-dark fs-14">Choose files</span>
                                            <input type="file" class="d-none" multiple>
                                        </label>
                                    </div> --}}
                                </div>

                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex gap-2">
                        {{-- <button type="button" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">
                            <i class="bi bi-eye"></i> Preview
                        </button> --}}
                        <button type="button" class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light"
                            id="sendButton">
                            <i class="bi bi-send"></i> Send Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal effect-scale md-wrapper" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Email</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="fs-15 p-0 m-0">Are you sure you want to send the renewal notice email?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light btn-wave waves-effect waves-light"
                        id="cancelRenewalEmail" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success label-btn label-end" id="confirmRenewalEmail">
                        Confirm Send
                        <i class="ri-mail-send-line label-btn-icon ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            let defaultEmailTemplate = null;

            tinymce.init({
                selector: 'textarea#renewal-email',
                setup: function(editor) {
                    editor.on('change', function(e) {
                        editor.save();
                    });
                },
                plugins: 'preview importcss searchreplace autolink autosave save directionality visualchars fullscreen link charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap quickbars accordion',
                // editimage_cors_hosts: ['picsum.photos'],
                menubar: false,
                toolbar: "bold italic underline strikethrough | align numlist bullist | fullscreen preview ",
                autosave_ask_before_unload: true,
                autosave_interval: '30s',
                autosave_prefix: '{path}{query}-{id}-',
                autosave_restore_when_empty: false,
                autosave_retention: '2m',
                image_advtab: false,
                importcss_append: true,
                automatic_uploads: false,
                branding: false,
                height: 500,
                image_caption: false,
                quickbars_selection_toolbar: 'bold italic | h2 h3 blockquote',
                noneditable_class: 'mceNonEditable',
                toolbar_mode: 'sliding',
                skin: 'oxide',
                content_css: 'default',
                quickbars_insert_toolbar: false,
                quickbars_selection_toolbar: false,
                content_style: 'body { font-family:Open Sans,Arial,sans-serif; font-size:15px } p,h1 {margin: 0px; padding:0px;}'
            });

            const renewalTbl = $('#renewal-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.policy_renewal_datatable', ['cover_no' => $cover_no]) !!}",
                },
                columns: [{
                        data: 'id',
                        searchable: true,
                        class: 'highlight-idx',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'name',
                        searchable: true,
                        class: 'highlight-2view-point'
                    },
                    {
                        data: 'renewal_type',
                        searchable: true,
                    },
                    {
                        data: 'expires',
                        searchable: true,
                    },
                    {
                        data: 'renewal_notice',
                        searchable: true,
                    },
                    {
                        data: 'actions',
                        searchable: true,
                        class: 'highlight-view-more',
                        sortable: false
                    },
                ],
                paging: false
            });

            $(document).on('click', '.renewal-view_doc', function(e) {
                e.preventDefault();
                const policy_no = $(this).data('policy_no');
                const recipient_type = $("#selected_renewal_action").val();
                let url = @json(route('docs.view.renewal_notice', ['policy_number' => '__POLICY_NO__', 'recipient_type' => '__RECIPIENT_TYPE__']))
                    .replace('__POLICY_NO__', policy_no)
                    .replace('__RECIPIENT_TYPE__', recipient_type);

                console.log(url)
                $.ajax({
                    url,
                    method: 'GET',
                    xhrFields: {
                        responseType: "blob"
                    },
                    success: function(data) {
                        if (data) {
                            let fileURL = URL.createObjectURL(data);
                            window.open(fileURL, '_blank', 'noopener,noreferrer');
                        }

                    },
                    error: function() {
                        toastr.error("An unexpected error occurred");
                    }
                });
            })

            $(document).on('click', '.renewal-doc_download', function(e) {
                e.preventDefault();
                const policy_no = $(this).data('policy_no');
                const recipient_type = $("#selected_renewal_action").val();
                let url =
                    `/docs/download/renewal_notice?policy_number=${policy_no}&recipient_type=${recipient_type}`;
                $.ajax({
                    url,
                    method: 'GET',
                    xhrFields: {
                        responseType: "blob"
                    },
                    success: function(response, status, xhr) {
                        const blob = new Blob([response], {
                            type: 'application/pdf'
                        });

                        const fileName = xhr.getResponseHeader('Content-Disposition')?.split(
                            'filename=')[1]?.trim() || 'document.pdf';

                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = fileName;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(link.href);
                    },
                    error: function(error) {
                        toastr.error("Failed to download file. Please try again.");
                    }
                });
            })

            $(document).on('click', '.remove-renewal_doc', function(e) {
                e.preventDefault();
                const title = $(this).data('title');
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Remove Renewal Notice',
                    text: `This action will remove the document: ${title}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isDismissed) {
                        return false;
                    }
                    const data = {
                        cover_no: "{!! $cover_no !!}",
                        id,
                    };
                    fetchWithCsrf("{!! route('cover.delete_renewal_notice') !!}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(data),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status == 201) {
                                toastr.success("Document removed successfully", 'Success');
                                renewalTbl.ajax.reload()
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors);
                            } else {
                                toastr.error("Failed to remove the document");
                            }
                        })
                        .catch(error => {
                            toastr.error("An unexpected error occurred");
                        });
                });
            })

            $('#generateNotice').click(function() {
                $.ajax({
                    url: '{{ route('cover.renewal_notice.generate') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        cover_no: '{{ $cover_no }}',
                        customer_id: '{{ $customer_id }}',
                    },
                    success: (response) => {
                        if (response.status == 201) {
                            toastr.success(response.message)
                            renewalTbl.ajax.reload()
                        } else {
                            toastr.error('An internal error occurred', 'Error')
                        }
                    }
                })
            })

            $("#renewalEmailForm").validate({
                errorClass: "errorClass",
                rules: {
                    emailTo: {
                        required: true
                    },
                    emailSubject: {
                        required: true
                    },
                    emailContent: {
                        required: true
                    },
                }
            })

            function validateEmailContent() {
                const content = tinymce.get('renewal-email').getContent();
                const requiredPlaceholders = [
                    "[Your Name]",
                    "[Your Position]",
                ];

                const missingPlaceholders = requiredPlaceholders.filter(placeholder =>
                    content.includes(placeholder)
                );

                if (missingPlaceholders.length > 0) {
                    return {
                        valid: false,
                        message: `Please replace the following:<br/> ${missingPlaceholders.join(', <br/>')}`
                    };
                }

                const textContent = content.replace(/<[^>]*>/g, '').trim();
                if (textContent.length < 10) {
                    return {
                        valid: false,
                        message: 'Email content is too short. Please provide more detailed information.'
                    };
                }

                return {
                    valid: true
                };
            }

            $('#sendButton').click(function(e) {
                e.preventDefault()
                $('#confirmModal').modal('hide')

                const validation = validateEmailContent();
                if (!validation.valid) {
                    toastr.error(validation.message, 'Incomplete Data')
                } else {
                    $('#view-renewaldocument-modal').modal('hide')
                    $('#confirmModal').modal('show')
                }
            })

            $('#cancelRenewalEmail').click(function(e) {
                e.preventDefault()
                $('#confirmModal').modal('hide')
                $('#view-renewaldocument-modal').modal('show')
            })

            $('#confirmRenewalEmail').click(function(e) {
                e.preventDefault()
                const form = $('#renewalEmailForm');
                const formData = new FormData(form[0]);
                formData.set('emailContent', tinymce.get('renewal-email').getContent());

                $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done(function(response) {
                        if (response.status == 201) {
                            $('#confirmModal').modal('hide')
                            $('#view-renewaldocument-modal').modal('hide')
                            toastr.success('Email sent successfully!')
                        } else {
                            if (response.status == 201) {
                                toastr.success(response.message)
                                renewalTbl.ajax.reload()
                            } else {
                                toastr.error('Failed to send Email')
                            }
                        }


                    })
                    .fail(function(xhr) {
                        toastr.error('An internal error occured', 'Error')
                    });
            })

            $(document).on('click', '.renewal-send_mail_doc', function(e) {
                e.preventDefault()
                const id = $(this).data('id')
                const doc_email = $(this).data('client_emails')
                const client_name = $(this).data('client_name')
                const client_docs = $(this).data('client_docs')
                const policy_no = $(this).data('policy_no')

                var selectedValue = $('#selected_renewal_action').val();
                if (selectedValue === 'cedant') {
                    $('#recipentType').val('cedant');
                    $('#staticRenewalLabel').html(
                        '<i class="bx bx-envelope"></i> Send Renewal Notice - To Cedant')
                    defaultEmailTemplate =
                        "<p>Dear,</p><br><p>Greetings.</p><br><p>The above subject risk is due for renewal as per the attached renewal notice.</p><p>Kindly advise if we should proceed to engage the reinsurers to hold the client covered We look forward to your positive feedback. </p><br><p>Best regards,<br>[Your Name]<br>[Your Position]</p > ";

                    if (doc_email.cedant) {
                        $('#emailTo').empty();
                        $.each(doc_email.cedant, function(index, email) {
                            $('#emailTo').append($('<option>', {
                                value: email.value,
                                text: email.text
                            }));
                        });
                    }

                } else if (selectedValue === 'reinsurer') {
                    $('#recipentType').val('reinsurer');
                    $('#staticRenewalLabel').html(
                        '<i class="bx bx-envelope"></i> Send Renewal Notice - To Reinsurer(s)')
                    defaultEmailTemplate =
                        "<p>Dear,</p><br><p>Greetings.</p><br><p>The above risk is due for renewal as per the attached renewal notice.</p><p>Kindly continue holding the client covered past expiry awaiting firm renewal terms and closings.</p><br><p>Best regards,<br>[Your Name]<br>[Your Position]</p>";

                    if (doc_email.reinsurer) {
                        $('#emailTo').empty();
                        $.each(doc_email.reinsurer, function(index, email) {
                            $('#emailTo').append($('<option>', {
                                value: email.value,
                                text: email.text
                            }));
                        });
                    }


                }
                tinymce.get('renewal-email').setContent(defaultEmailTemplate);

                $('#emailTo').select2('destroy').find('option').prop('selected', true).end().select2();
                $('#emailSubject').val(`Policy Renewal Notice - ${policy_no}`);
                $('#policyId').val(id);

                loadAttachements(client_docs?.attachments, policy_no, selectedValue)
            })

            function loadAttachements(attachments, policy_no, recipient_type) {
                let container = $('#attachments-container');
                container.html('');
                let totalSize = 0;

                if (typeof attachments !== 'undefined' && attachments.length > 0) {
                    attachments.forEach((file, index) => {

                        const viewDocUrl = {!! json_encode(
                            route('docs.view.renewal_notice', ['policy_number' => '__POLICY_NO__', 'recipient_type' => '__RECIPIENT_TYPE__']),
                        ) !!}
                            .replace('__POLICY_NO__', policy_no)
                            .replace('__RECIPIENT_TYPE__', recipient_type);
                        let attachmentHtml = `
                            <a href="${viewDocUrl}" class="mail-attachment border mr-2" target="_blank">
                                <div class="attachment-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" baseProfile="tiny" viewBox="0 0 512 512"><path fill="#FFF" d="M422.3 477.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z"></path><path fill="#2B669F" d="M333.6 6H103.5C88 6 75.4 18.6 75.4 34.1v443.8c0 15.5 12.6 28.1 28.1 28.1h305c15.5 0 28.1-12.6 28.1-28.1V109.1L333.6 6zm88.7 471.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z"></path><path fill="#084272" d="M333.6 6v103.1h103z"></path><g><path fill="#084272" d="M465.9 450.8H46.1V308c0-9.8 7.9-17.7 17.7-17.7h384.3c9.8 0 17.7 7.9 17.7 17.7v142.8z"></path><path fill="#1A252D" d="M436.6 450.8v19.5l29.3-19.5zM75.4 450.8v19.5l-29.3-19.5z"></path><path fill="#2B669F" d="M64.1 308.4h383.7v124.5H64.1z"></path></g><g fill="#2B669F"><path d="M298.3 78.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM298.3 110.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM391.8 142.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 174.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 206.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 238.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 270.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4z"></path></g><g fill="#FFF"><path d="M229.3 373.3c0 6.9-1.6 12.5-4.7 16.7-3.1 4.2-7.5 6.3-13.2 6.3-2.2 0-4.2-.4-5.9-1.3-1.7-.9-3.2-2.1-4.5-3.7v21.8h-14.4v-63.8h13.6l.4 5c1.3-1.9 2.8-3.3 4.6-4.3 1.8-1 3.8-1.5 6.1-1.5 5.7 0 10.1 2.2 13.3 6.6 3.1 4.4 4.7 10.2 4.7 17.4v.8zm-14.3-.9c0-3.9-.6-7-1.7-9.4-1.1-2.4-3-3.5-5.4-3.5-1.6 0-3 .3-4.1.9-1.1.6-2 1.5-2.7 2.6v19.2c.7 1 1.6 1.7 2.7 2.2 1.1.5 2.5.7 4.1.7 2.5 0 4.3-1 5.4-3.1 1.1-2.1 1.6-5 1.6-8.7v-.9zM239.8 372.4c0-7.2 1.6-13 4.7-17.4 3.1-4.4 7.6-6.6 13.3-6.6 2.1 0 4 .5 5.8 1.5 1.7 1 3.3 2.4 4.6 4.2V329h14.4v66.4H270l-1-5.6c-1.4 2.1-3 3.7-4.9 4.8-1.9 1.1-4 1.7-6.4 1.7-5.7 0-10.1-2.1-13.2-6.3-3.1-4.2-4.7-9.7-4.7-16.6v-1zm14.4.9c0 3.7.5 6.7 1.6 8.7 1.1 2.1 2.9 3.1 5.5 3.1 1.5 0 2.8-.3 4-.8 1.1-.6 2.1-1.4 2.8-2.4v-18.6c-.7-1.2-1.7-2.2-2.8-2.8-1.1-.7-2.4-1-3.9-1-2.6 0-4.4 1.2-5.5 3.5-1.1 2.4-1.7 5.5-1.7 9.4v.9zM300 395.4v-36.1h-6.6v-10h6.6v-4.8c0-5.3 1.6-9.3 4.8-12.2 3.2-2.9 7.7-4.3 13.5-4.3 1.1 0 2.2.1 3.3.2 1.1.2 2.4.4 3.8.7l-1.1 10.6c-.8-.1-1.5-.2-2.1-.3-.6-.1-1.3-.1-2.2-.1-1.8 0-3.2.5-4.2 1.4-1 .9-1.4 2.3-1.4 4v4.8h9.1v10h-9.1v36.1H300z"></path></g></svg>
                                </div>
                                <div class="lh-1">
                                    <p class="attachment-name text-truncate">${file.name}</p>
                                    <p class="mb-0 fs-11 text-muted">${file.size}mb</p>
                                </div>
                            </a>
                        `;

                        const isCedantDoc = file.name.includes('Cedant_Renewal_Notice') &&
                            recipient_type === 'cedant';
                        const isReinsurerDoc = file.name.includes('Reinsurer_Renewal_Notice') &&
                            recipient_type === 'reinsurer';

                        if (isCedantDoc || isReinsurerDoc) {
                            container.append(attachmentHtml);
                            totalSize += parseFloat(file.size);
                        }
                    });
                } else {
                    container.append('<p>No attachments available.</p>');
                }
                $('#attachment_status').html(`(${totalSize.toFixed(2)}mb):`)
            }
        })
    </script>
@endpush
