@extends('layouts.app', [
    'pageTitle' => 'Cover Reports - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Cover Reports</p>
            <span class="fs-semibold text-muted"></span>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cover Reports</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                {{-- <div class="card-header">
                    <div class="card-title">Justified Nav Tabs</div>
                </div> --}}
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3 nav-justified nav-style-1 d-sm-flex d-block" role="tablist">
                        <li class="nav-item active">
                            <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#home1-justified"
                                aria-selected="false">Cover Placement</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" role="tab" href="#about1-justified"
                                aria-selected="false">Covers by Type</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" role="tab" href="#service1-justified"
                                aria-selected="true">Covers Ending</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" role="tab" href="#license1-justified"
                                aria-selected="false">Renewed Covers</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane text-muted" id="home1-justified" role="tabpanel">
                            Contrary to popular belief, Lorem Ipsum is not simply
                            random text. It has roots in a piece of classical Latin
                            literature from 45 BC,
                            <b>Making it over 2000 years old</b>. Richard McClintock,
                            a Latin professor at Hampden-Sydney College in Virginia,
                            looked up one of the more obscure Latin words,
                            consectetur.
                        </div>
                        <div class="tab-pane text-muted" id="about1-justified" role="tabpanel">
                            <b>Lorem Ipsum is simply dummy</b> text of the printing
                            and typesetting industry. Lorem Ipsum has been the
                            industry's standard dummy text ever since the 1500s, when
                            an unknown printer took a galley of type and scrambled it
                            to make a type specimen book. It has survived not only
                            five centuries.
                        </div>
                        <div class="tab-pane show active text-muted" id="service1-justified" role="tabpanel">
                            There are many variations of passages of
                            <b>Lorem Ipsum available</b>, but the majority have
                            suffered alteration in some form, by injected humour, or
                            randomised words which don't look even slightly
                            believable. If you are going to use a passage of Lorem
                            Ipsum, you need to be sure there isn't anything.
                        </div>
                        <div class="tab-pane text-muted" id="license1-justified" role="tabpanel">
                            It is a long established fact that a reader will be
                            distracted by the
                            <b><i>Readable content</i></b> of a page when looking at
                            its layout. The point of using Lorem Ipsum is that it has
                            a more-or-less normal distribution of letters, as opposed
                            to using 'Content here, content here', making it look like
                            readable English.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
