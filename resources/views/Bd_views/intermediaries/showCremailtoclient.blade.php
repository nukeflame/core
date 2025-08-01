@extends('layouts.intermediaries.base')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <label>EMAIL STARTERPACK DRAFT</label>
                <div class="card-body">
                    <div class="tab-content">

                        <form action="{{ route('sendDraftEmailCrClient') }}" method="POST" enctype="multipart/form-data"
                            id="crmEmail">
                            @csrf

                            <input type="text" name="prospectId" value="{{ $prospectId }} " hidden>
                            <div class="form-group">
                                <label for="recipient" class="font-weight-bold">
                                    <i class="fas fa-at mr-2"></i>Recipient Email
                                </label>
                                <input type="email" class="form-control" id="recipient" name="recipient"
                                    value="{{ $mail }}" required>
                            </div>

                            <div class="form-group">
                                <label for="cc" class="font-weight-bold">
                                    <i class="fas fa-envelope-open-text mr-2"></i>CC Email (Optional)
                                </label>
                                <textarea class="form-control" id="cc" name="cc" rows="2"
                                    placeholder="Enter 1 or multiple emails eg example1@email.com, example2@email.com; example3@email.com"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="subject" class="font-weight-bold">
                                    <i class="fas fa-heading mr-2"></i>Email Subject
                                </label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                    placeholder="Project Update and Next Steps" required>
                            </div>

                            <div class="form-group">
                                <label for="content" class="font-weight-bold">
                                    <i class="fas fa-pencil-alt mr-2"></i>Email Content
                                </label>

                                <textarea class="form-control" id="content" name="content" rows="10"
                                    style="text-align: left !important; 
                                        height: 434px; 
                                        padding-left: 10px; 
                                        line-height: 1.5;
                                        text-align-last: left;
                                        white-space: pre-wrap;
                                        word-wrap: break-word;
                                        hyphens: auto;"
                                    required>
Dear XXXX,
                        
On behalf of Acentria Insurance Brokers Limited, I take this opportunity to welcome XXXX (Name of client) on board. We are grateful for the appointment to serve your company as the insurance broker and shall reiterate by offering bespoke service.
                        
We have placed the scheme on cover effective dd/mm/yr to dd/mm/yr with XXX (Insurance company).   
                        
As part of onboarding, I wish to e-introduce the day-to-day team that shall be handling your scheme:
                        
Tracy Miranyi / Fiona Karanu – tmiranyi@acentriagroup.com / fkaranu@acentriagroup.com and group email- underwriting@acentriagroup.com – 0727-112777 – additions/deletions/statements
Wanjiru Nyaga – wnyaga@acentriagroup.com – 0726-625438 - All day-to-day escalations/performance reviews etc
Mandela Kariuki - claims@acentriagroup.com
                        
This team is backed by a larger team in their absence.
                                                        
Emergency numbers – 0731-200999 or 0716 -200999
                                                        
 We have attached the following as part of the staff starter pack to ensure compliance and seamless operation through the year:
                                                        
Insurance company provider panel 
                        Outpatient claim form – for members who may visit providers outside the panel
 Member application form – for new members joining the scheme
 Reimbursement requirements
                        
Attached also find the following:
                        
Service level agreement for your perusal and execution
Sample memo to staff on changes
Implementation timetable on how we plan to engage and service the scheme
                        
We would suggest to have a member education session for staff, and implementation meeting with the team in charge of the scheme at your convenience to plan the yearly activities. This is preferably best planned in the first month of cover. 
                        
Should you need further clarification or assistance, we are at your service.  Do not hesitate to reach out.
                        
Once again, thank you for your business support and we assure you of our seamless service throughout the year. 
                        
Kind Regards,
                        
xxxx     xxx
Client Relations Executive
+254 7xx xxx xxx
xxxx@acentriagroup.com
                        
Acentria Group - Insurance Broking
West Park Towers, 9th floor, Mpesi Lane, Muthithi Road
P.O Box 5864-00100 Nairobi, Kenya |  +254 705 200 222 
info@acentriagroup.com  |  www.acentriagroup.com
</textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-lg">
                                    <i class="fas fa-paper-plane mr-2"></i>Send Email
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.getElementById('crmEmail').addEventListener('submit', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to send this email?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sending email...',
                        text: 'Please wait while we process your request.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    let formData = new FormData(this);

                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {

                            Swal.fire({
                                icon: 'success',
                                title: 'Email Sent!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = data.redirect;
                            });


                        })
                        .catch(error => {

                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong while sending the email.',
                            });
                        });
                }
            });
        });
    </script>

    <style>
        textarea {
            height: 0px;
        }
    </style>
@endsection
