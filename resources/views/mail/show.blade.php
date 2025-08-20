@extends('layouts.app', [
    'pageTitle' => 'Mail Inbox - ' . $company->company_name,
])

@section('content')
    {{-- <style>
        .email-container {
            min-height: calc(100vh - 130px);
            background: transparent;
        }

        .email-content {
            background: transparent;
            border-radius: 12px;
            padding: 3rem;
            max-width: 400px;
            margin: 0 auto;
        }

        .envelope-icon {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .envelope-icon::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
        }

        .envelope-icon::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            width: 20px;
            height: 20px;
            border-left: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            transform: rotate(-45deg);
        }

        .envelope-icon i {
            color: #333;
            font-size: 5rem;
            z-index: 1;
        }

        .email-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .email-subtitle {
            color: #666;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 0;
        }

        .side-triangles {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            transition: all 0.3s ease;
        }

        .email-content:hover .side-triangles {
            transform: translateY(-50%) scale(1.1);
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        #bod_text_preview {
            height: 200px;
            overflow-x: hidden;
            overflow-y: auto;
        }
    </style> --}}

    <div class="container-fluid">
        <div class="main-mail-container p-2 gap-2 d-flex">
            @include('admin.email.includes._mail_navigation')

            {{-- email navigation --}}
            @include('admin.email.includes._total_emails_nav')

            <div class="mails-information border" id="email-detail-view">
                <div class="mail-info-header d-flex flex-wrap gap-2 align-items-center">
                    <div class="me-1">
                        <span class="avatar avatar-md online me-2 avatar-rounded mail-msg-avatar">
                            <img src="/assets/images/faces/12.jpg" alt="">
                        </span>
                    </div>
                    <div class="flex-fill">
                        <h6 class="mb-0 fw-semibold">{{ $email?->from_name }}</h6>
                        <span class="text-muted fs-12">{{ $email?->from_email }}</span>
                    </div>
                    <div class="mail-action-icons">
                        <button class="btn btn-icon btn-light" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Starred">
                            <i class="ri-star-line"></i>
                        </button>
                        <button class="btn btn-icon btn-light ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Archive">
                            <i class="ri-inbox-archive-line"></i>
                        </button>
                        <button class="btn btn-icon btn-light ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Report spam">
                            <i class="ri-spam-2-line"></i>
                        </button>
                        <button class="btn btn-icon btn-light ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Delete">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                        <button class="btn btn-icon btn-light ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Reply">
                            <i class="ri-reply-line"></i>
                        </button>
                    </div>
                    <div class="responsive-mail-action-icons">
                        <div class="dropdown">
                            <button class="btn btn-icon btn-light btn-wave waves-light waves-effect waves-light"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i
                                            class="ri-star-line me-1 align-middle d-inline-block"></i>Starred</a></li>
                                <li><a class="dropdown-item" href="#"><i
                                            class="ri-inbox-archive-line me-1 align-middle d-inline-block"></i>Archive</a>
                                </li>
                                <li><a class="dropdown-item" href="#"><i
                                            class="ri-spam-2-line me-1 align-middle d-inline-block"></i>Report Spam</a>
                                </li>
                                <li><a class="dropdown-item" href="#"><i
                                            class="ri-delete-bin-line me-1 align-middle d-inline-block"></i>Delete</a></li>
                                <li><a class="dropdown-item" href="#"><i
                                            class="ri-reply-line me-1 align-middle d-inline-block"></i>Reply</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-icon btn-light ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                </div>

                <div class="mail-info-body p-4" id="mail-info-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-between mb-4">
                        <div>
                            <p class="fs-20 fw-semibold mb-0">{{ $email?->subject ?? 'No Subject' }}</p>
                        </div>
                        <div class="float-end">
                            <span class="me-2 fs-12 text-muted">Oct-22-2022,03:05PM</span>
                        </div>
                    </div>
                    <div class="main-mail-content mb-4" id="bod_text_preview">
                        <iframe srcdoc="{{ $email?->body_html }}" style="width:100%;height:400px;border:none;"
                            sandbox="allow-same-origin allow-popups allow-forms allow-scripts"></iframe>
                    </div>
                    <div class="mail-attachments mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="mb-0">
                                <span class="fs-14 fw-semibold">
                                    <i class="ri-attachment-2 me-1 align-middle"></i>
                                    Attachments 0.1mb:
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-success-light">Download All</button>
                            </div>
                        </div>
                        <div class="mt-2 d-flex">
                            <a href="#" target="_blank" class="mail-attachment border me-2">
                                <div class="attachment-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" baseProfile="tiny" viewBox="0 0 512 512">
                                        <path fill="#FFF"
                                            d="M422.3 477.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z" />
                                        <path fill="#2B669F"
                                            d="M333.6 6H103.5C88 6 75.4 18.6 75.4 34.1v443.8c0 15.5 12.6 28.1 28.1 28.1h305c15.5 0 28.1-12.6 28.1-28.1V109.1L333.6 6zm88.7 471.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z" />
                                        <path fill="#084272" d="M333.6 6v103.1h103z" />
                                        <g>
                                            <path fill="#084272"
                                                d="M465.9 450.8H46.1V308c0-9.8 7.9-17.7 17.7-17.7h384.3c9.8 0 17.7 7.9 17.7 17.7v142.8z" />
                                            <path fill="#1A252D"
                                                d="M436.6 450.8v19.5l29.3-19.5zM75.4 450.8v19.5l-29.3-19.5z" />
                                            <path fill="#2B669F" d="M64.1 308.4h383.7v124.5H64.1z" />
                                        </g>
                                        <g fill="#2B669F">
                                            <path
                                                d="M298.3 78.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM298.3 110.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM391.8 142.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 174.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 206.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 238.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 270.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4z" />
                                        </g>
                                        <g fill="#FFF">
                                            <path
                                                d="M229.3 373.3c0 6.9-1.6 12.5-4.7 16.7-3.1 4.2-7.5 6.3-13.2 6.3-2.2 0-4.2-.4-5.9-1.3-1.7-.9-3.2-2.1-4.5-3.7v21.8h-14.4v-63.8h13.6l.4 5c1.3-1.9 2.8-3.3 4.6-4.3 1.8-1 3.8-1.5 6.1-1.5 5.7 0 10.1 2.2 13.3 6.6 3.1 4.4 4.7 10.2 4.7 17.4v.8zm-14.3-.9c0-3.9-.6-7-1.7-9.4-1.1-2.4-3-3.5-5.4-3.5-1.6 0-3 .3-4.1.9-1.1.6-2 1.5-2.7 2.6v19.2c.7 1 1.6 1.7 2.7 2.2 1.1.5 2.5.7 4.1.7 2.5 0 4.3-1 5.4-3.1 1.1-2.1 1.6-5 1.6-8.7v-.9zM239.8 372.4c0-7.2 1.6-13 4.7-17.4 3.1-4.4 7.6-6.6 13.3-6.6 2.1 0 4 .5 5.8 1.5 1.7 1 3.3 2.4 4.6 4.2V329h14.4v66.4H270l-1-5.6c-1.4 2.1-3 3.7-4.9 4.8-1.9 1.1-4 1.7-6.4 1.7-5.7 0-10.1-2.1-13.2-6.3-3.1-4.2-4.7-9.7-4.7-16.6v-1zm14.4.9c0 3.7.5 6.7 1.6 8.7 1.1 2.1 2.9 3.1 5.5 3.1 1.5 0 2.8-.3 4-.8 1.1-.6 2.1-1.4 2.8-2.4v-18.6c-.7-1.2-1.7-2.2-2.8-2.8-1.1-.7-2.4-1-3.9-1-2.6 0-4.4 1.2-5.5 3.5-1.1 2.4-1.7 5.5-1.7 9.4v.9zM300 395.4v-36.1h-6.6v-10h6.6v-4.8c0-5.3 1.6-9.3 4.8-12.2 3.2-2.9 7.7-4.3 13.5-4.3 1.1 0 2.2.1 3.3.2 1.1.2 2.4.4 3.8.7l-1.1 10.6c-.8-.1-1.5-.2-2.1-.3-.6-.1-1.3-.1-2.2-.1-1.8 0-3.2.5-4.2 1.4-1 .9-1.4 2.3-1.4 4v4.8h9.1v10h-9.1v36.1H300z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="lh-1">
                                    <p class="mb-1 attachment-name text-truncate">
                                        file.pdf
                                    </p>
                                    <p class="mb-0 fs-11 text-muted">
                                        400kb
                                    </p>
                                </div>
                            </a>
                        </div>
                        {{-- @if (!empty($email->attachments))
                            @php
                                $attachments = json_decode($email->attachments, true);
                                $totalSizeBytes = array_sum(array_column($attachments, 'size'));
                                $totalSizeMb = number_format($totalSizeBytes / 1048576, 1);
                            @endphp

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="mb-0">
                                    <span class="fs-14 fw-semibold">
                                        <i class="ri-attachment-2 me-1 align-middle"></i>
                                        Attachments ({{ $totalSizeMb }}mb):
                                    </span>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-success-light">Download All</button>
                                </div>
                            </div>
                        @endif --}}

                        {{-- @if (!empty($email->attachments))
                            @php
                                $attachments = json_decode($email->attachments, true);
                            @endphp

                            <div class="mt-2 d-flex">
                                @foreach ($attachments as $file)
                                    <a href="{{ asset($file['path']) }}" target="_blank"
                                        class="mail-attachment border me-2">
                                        <div class="attachment-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" baseProfile="tiny"
                                                viewBox="0 0 512 512">
                                                <path fill="#FFF"
                                                    d="M422.3 477.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z" />
                                                <path fill="#2B669F"
                                                    d="M333.6 6H103.5C88 6 75.4 18.6 75.4 34.1v443.8c0 15.5 12.6 28.1 28.1 28.1h305c15.5 0 28.1-12.6 28.1-28.1V109.1L333.6 6zm88.7 471.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z" />
                                                <path fill="#084272" d="M333.6 6v103.1h103z" />
                                                <g>
                                                    <path fill="#084272"
                                                        d="M465.9 450.8H46.1V308c0-9.8 7.9-17.7 17.7-17.7h384.3c9.8 0 17.7 7.9 17.7 17.7v142.8z" />
                                                    <path fill="#1A252D"
                                                        d="M436.6 450.8v19.5l29.3-19.5zM75.4 450.8v19.5l-29.3-19.5z" />
                                                    <path fill="#2B669F" d="M64.1 308.4h383.7v124.5H64.1z" />
                                                </g>
                                                <g fill="#2B669F">
                                                    <path
                                                        d="M298.3 78.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM298.3 110.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM391.8 142.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 174.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 206.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 238.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 270.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4z" />
                                                </g>
                                                <g fill="#FFF">
                                                    <path
                                                        d="M229.3 373.3c0 6.9-1.6 12.5-4.7 16.7-3.1 4.2-7.5 6.3-13.2 6.3-2.2 0-4.2-.4-5.9-1.3-1.7-.9-3.2-2.1-4.5-3.7v21.8h-14.4v-63.8h13.6l.4 5c1.3-1.9 2.8-3.3 4.6-4.3 1.8-1 3.8-1.5 6.1-1.5 5.7 0 10.1 2.2 13.3 6.6 3.1 4.4 4.7 10.2 4.7 17.4v.8zm-14.3-.9c0-3.9-.6-7-1.7-9.4-1.1-2.4-3-3.5-5.4-3.5-1.6 0-3 .3-4.1.9-1.1.6-2 1.5-2.7 2.6v19.2c.7 1 1.6 1.7 2.7 2.2 1.1.5 2.5.7 4.1.7 2.5 0 4.3-1 5.4-3.1 1.1-2.1 1.6-5 1.6-8.7v-.9zM239.8 372.4c0-7.2 1.6-13 4.7-17.4 3.1-4.4 7.6-6.6 13.3-6.6 2.1 0 4 .5 5.8 1.5 1.7 1 3.3 2.4 4.6 4.2V329h14.4v66.4H270l-1-5.6c-1.4 2.1-3 3.7-4.9 4.8-1.9 1.1-4 1.7-6.4 1.7-5.7 0-10.1-2.1-13.2-6.3-3.1-4.2-4.7-9.7-4.7-16.6v-1zm14.4.9c0 3.7.5 6.7 1.6 8.7 1.1 2.1 2.9 3.1 5.5 3.1 1.5 0 2.8-.3 4-.8 1.1-.6 2.1-1.4 2.8-2.4v-18.6c-.7-1.2-1.7-2.2-2.8-2.8-1.1-.7-2.4-1-3.9-1-2.6 0-4.4 1.2-5.5 3.5-1.1 2.4-1.7 5.5-1.7 9.4v.9zM300 395.4v-36.1h-6.6v-10h6.6v-4.8c0-5.3 1.6-9.3 4.8-12.2 3.2-2.9 7.7-4.3 13.5-4.3 1.1 0 2.2.1 3.3.2 1.1.2 2.4.4 3.8.7l-1.1 10.6c-.8-.1-1.5-.2-2.1-.3-.6-.1-1.3-.1-2.2-.1-1.8 0-3.2.5-4.2 1.4-1 .9-1.4 2.3-1.4 4v4.8h9.1v10h-9.1v36.1H300z" />
                                                </g>
                                            </svg>
                                        </div>
                                        <div class="lh-1">
                                            <p class="mb-1 attachment-name text-truncate">
                                                {{ $file['filename'] }}
                                            </p>
                                            <p class="mb-0 fs-11 text-muted">
                                                @php
                                                    $size = $file['size'];
                                                    if ($size >= 1048576) {
                                                        echo number_format($size / 1048576, 2) . 'MB';
                                                    } else {
                                                        echo number_format($size / 1024, 0) . 'KB';
                                                    }
                                                @endphp
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif --}}

                        {{-- <div class="mt-2 d-flex">
                            <a href="javascript:void(0);" class="mail-attachment border">
                                <div class="attachment-icon"><svg xmlns="http://www.w3.org/2000/svg" baseProfile="tiny"
                                        viewBox="0 0 512 512">
                                        <path fill="#FFF"
                                            d="M422.3 477.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z" />
                                        <path fill="#2B669F"
                                            d="M333.6 6H103.5C88 6 75.4 18.6 75.4 34.1v443.8c0 15.5 12.6 28.1 28.1 28.1h305c15.5 0 28.1-12.6 28.1-28.1V109.1L333.6 6zm88.7 471.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z" />
                                        <path fill="#084272" d="M333.6 6v103.1h103z" />
                                        <g>
                                            <path fill="#084272"
                                                d="M465.9 450.8H46.1V308c0-9.8 7.9-17.7 17.7-17.7h384.3c9.8 0 17.7 7.9 17.7 17.7v142.8z" />
                                            <path fill="#1A252D"
                                                d="M436.6 450.8v19.5l29.3-19.5zM75.4 450.8v19.5l-29.3-19.5z" />
                                            <path fill="#2B669F" d="M64.1 308.4h383.7v124.5H64.1z" />
                                        </g>
                                        <g fill="#2B669F">
                                            <path
                                                d="M298.3 78.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM298.3 110.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM391.8 142.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 174.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 206.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 238.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 270.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4z" />
                                        </g>
                                        <g fill="#FFF">
                                            <path
                                                d="M229.3 373.3c0 6.9-1.6 12.5-4.7 16.7-3.1 4.2-7.5 6.3-13.2 6.3-2.2 0-4.2-.4-5.9-1.3-1.7-.9-3.2-2.1-4.5-3.7v21.8h-14.4v-63.8h13.6l.4 5c1.3-1.9 2.8-3.3 4.6-4.3 1.8-1 3.8-1.5 6.1-1.5 5.7 0 10.1 2.2 13.3 6.6 3.1 4.4 4.7 10.2 4.7 17.4v.8zm-14.3-.9c0-3.9-.6-7-1.7-9.4-1.1-2.4-3-3.5-5.4-3.5-1.6 0-3 .3-4.1.9-1.1.6-2 1.5-2.7 2.6v19.2c.7 1 1.6 1.7 2.7 2.2 1.1.5 2.5.7 4.1.7 2.5 0 4.3-1 5.4-3.1 1.1-2.1 1.6-5 1.6-8.7v-.9zM239.8 372.4c0-7.2 1.6-13 4.7-17.4 3.1-4.4 7.6-6.6 13.3-6.6 2.1 0 4 .5 5.8 1.5 1.7 1 3.3 2.4 4.6 4.2V329h14.4v66.4H270l-1-5.6c-1.4 2.1-3 3.7-4.9 4.8-1.9 1.1-4 1.7-6.4 1.7-5.7 0-10.1-2.1-13.2-6.3-3.1-4.2-4.7-9.7-4.7-16.6v-1zm14.4.9c0 3.7.5 6.7 1.6 8.7 1.1 2.1 2.9 3.1 5.5 3.1 1.5 0 2.8-.3 4-.8 1.1-.6 2.1-1.4 2.8-2.4v-18.6c-.7-1.2-1.7-2.2-2.8-2.8-1.1-.7-2.4-1-3.9-1-2.6 0-4.4 1.2-5.5 3.5-1.1 2.4-1.7 5.5-1.7 9.4v.9zM300 395.4v-36.1h-6.6v-10h6.6v-4.8c0-5.3 1.6-9.3 4.8-12.2 3.2-2.9 7.7-4.3 13.5-4.3 1.1 0 2.2.1 3.3.2 1.1.2 2.4.4 3.8.7l-1.1 10.6c-.8-.1-1.5-.2-2.1-.3-.6-.1-1.3-.1-2.2-.1-1.8 0-3.2.5-4.2 1.4-1 .9-1.4 2.3-1.4 4v4.8h9.1v10h-9.1v36.1H300z" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="lh-1">
                                    <p class="mb-1 attachment-name text-truncate">
                                        Earth_Archeology_2.21-4.pdf
                                    </p>
                                    <p class="mb-0 fs-11 text-muted">
                                        0.85MB
                                    </p>
                                </div>
                            </a>
                            <a href="javascript:void(0);" class="mail-attachment ms-2 border">
                                <div class="attachment-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                        <path fill="#FFF"
                                            d="M422.3 477.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z" />
                                        <path fill="#24A885"
                                            d="M333.6 6H103.5C88 6 75.4 18.6 75.4 34.1v443.8c0 15.5 12.6 28.1 27.1 28.1h305c16.5 0 29.1-12.6 29.1-28.1V109.1L333.6 6zm88.7 471.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z" />
                                        <path fill="#16876B"
                                            d="M333.6 6v103.1h103zM465.9 450.8H46.1V308c0-9.8 7.9-17.7 17.7-17.7h384.3c9.8 0 17.7 7.9 17.7 17.7v142.8z" />
                                        <path fill="#59E0B9"
                                            d="M436.6 450.8v19.5l29.3-19.5zM75.4 450.8v19.5l-29.3-19.5z" />
                                        <path fill="#2EBA98" d="M64.1 308.4h383.7v124.5H64.1z" />
                                        <path fill="#16876B"
                                            d="M298.3 78.6h-177a6.7 6.7 0 0 1 0-13.4h177a6.7 6.7 0 0 1 0 13.4zM298.3 110.6h-177a6.7 6.7 0 0 1 0-13.4h177a6.7 6.7 0 0 1 0 13.4zM391.8 142.5H121.3a6.7 6.7 0 0 1 0-13.4h270.5a6.7 6.7 0 0 1 0 13.4zM391.8 174.5H121.3a6.7 6.7 0 0 1 0-13.4h270.5a6.7 6.7 0 0 1 0 13.4zM391.8 206.5H121.3a6.7 6.7 0 0 1 0-13.4h270.5a6.7 6.7 0 0 1 0 13.4zM391.8 238.4H121.3a6.7 6.7 0 0 1 0-13.4h270.5a6.7 6.7 0 0 1 0 13.4zM391.8 270.4H121.3a6.7 6.7 0 0 1 0-13.4h270.5a6.7 6.7 0 0 1 0 13.4z" />
                                        <path fill="#FFF"
                                            d="M191.3 349.7v43.9c0 5.4-1.4 9.6-4.3 12.5-2.9 2.9-6.9 4.4-12.1 4.4-1.2 0-2.2-.1-3.2-.2s-2-.3-3.1-.5l.6-10.2c.8.2 1.4.3 2 .4s1.2.1 2.1.1c1.5 0 2.6-.5 3.4-1.6.8-1.1 1.2-2.7 1.2-4.8v-43.9h13.4zm-.2-10h-13.6v-9.1h13.6v9.1zM244.8 372.3c0 6.5-1.5 11.7-4.4 15.7-2.9 3.9-7.1 5.9-12.4 5.9-2.1 0-3.9-.4-5.5-1.2-1.6-.8-3-2-4.2-3.5v20.5h-13.5v-60h12.8l.4 4.7c1.2-1.8 2.6-3.1 4.3-4.1 1.7-1 3.6-1.4 5.7-1.4 5.4 0 9.5 2.1 12.5 6.2s4.4 9.6 4.4 16.3v.9zm-13.5-.8c0-3.7-.5-6.6-1.6-8.8-1.1-2.2-2.8-3.3-5.1-3.3-1.5 0-2.8.3-3.9.8-1.1.6-1.9 1.4-2.5 2.4v18.1c.6.9 1.5 1.6 2.5 2.1 1.1.5 2.4.7 3.9.7 2.4 0 4.1-1 5.1-2.9 1-2 1.5-4.7 1.5-8.2v-.9zM275.7 393.9c-6.6 0-11.7-2-15.4-6-3.7-4-5.6-9-5.6-15.1v-1.5c0-6.6 1.8-12 5.3-16.2 3.5-4.2 8.6-6.2 15.2-6.2 5.8 0 10.4 1.7 13.6 5.2 3.2 3.5 4.8 8.3 4.8 14.4v7.1h-24.8l-.1.2c.2 2.3 1.1 4.1 2.5 5.5 1.5 1.4 3.6 2.1 6.3 2.1 2.6 0 4.7-.2 6.5-.6 1.7-.4 3.7-1.1 5.9-2l3.2 8.3c-1.9 1.4-4.4 2.5-7.5 3.4-3.1 1-6.4 1.4-9.9 1.4zm-.5-34.5c-2.2 0-3.8.7-4.8 2.1-1 1.4-1.6 3.2-1.8 5.6l.1.2h11.7v-1c0-2.2-.4-3.9-1.2-5.1-.7-1.3-2.1-1.8-4-1.8zM303.3 371.5c0-6.8 1.5-12.2 4.4-16.3 3-4.1 7.1-6.2 12.5-6.2 2.3 0 4.3.5 6 1.6 1.7 1.1 3.2 2.6 4.5 4.6l.9-5.4h11.9v42.9c0 5.7-2 10.1-5.9 13.3-3.9 3.1-9.3 4.7-16.2 4.7-2.2 0-4.6-.3-7.1-.9-2.5-.6-4.8-1.4-7.1-2.5l2.3-10c1.9.9 3.8 1.5 5.6 1.9 1.8.4 3.8.6 6.1.6 2.9 0 5-.6 6.4-1.7 1.4-1.1 2.1-3 2.1-5.4v-3.4c-1.2 1.6-2.7 2.8-4.3 3.6-1.6.8-3.5 1.2-5.5 1.2-5.3 0-9.5-2-12.4-5.9-2.9-4-4.4-9.2-4.4-15.7v-1zm13.5.8c0 3.5.5 6.3 1.5 8.2 1 2 2.8 2.9 5.2 2.9 1.5 0 2.7-.2 3.8-.7 1.1-.5 1.9-1.1 2.6-2v-18.2c-.7-1-1.5-1.8-2.6-2.4-1-.5-2.3-.8-3.7-.8-2.4 0-4.1 1.1-5.2 3.3-1.1 2.2-1.6 5.2-1.6 8.8v.9z" />
                                    </svg>
                                </div>
                                <div class="lh-1">
                                    <p class="mb-1 attachment-name text-truncate">
                                        Planets_Image.Jpeg
                                    </p>
                                    <p class="mb-0 fs-11 text-muted">
                                        457KB
                                    </p>
                                </div>
                            </a>
                        </div> --}}
                    </div>
                    <div class="mb-3">
                        <span class="fs-14 fw-semibold"><i
                                class="ri-reply-all-line me-1 align-middle d-inline-block"></i>Reply:</span>
                    </div>
                    <div class="mail-reply">
                        <div id="mail-reply-editor"></div>
                    </div>
                </div>
                <div class="mail-info-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <div>
                        <button class="btn btn-icon btn-light" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Print">
                            <i class="ri-printer-line"></i>
                        </button>
                        <button class="btn btn-icon btn-light ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Mark as read">
                            <i class="ri-mail-open-line"></i>
                        </button>
                        <button class="btn btn-icon btn-light ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Reload">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
                    <div>
                        {{-- <button class="btn btn-secondary">
                            <i class="ri-share-forward-line me-1 d-inline-block align-middle"></i>Forward
                        </button> --}}
                        <button class="btn btn-danger ms-1">
                            <i class="ri-reply-all-line me-1 d-inline-block align-middle"></i>Reply
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

{{-- @include('admin.email.includes._connection_script') --}}

@push('script')
    <script>
        $(document).ready(function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            var toolbarOptions = [
                [{
                    'header': [1, 2, 3, 4, 5, 6, false]
                }],
                [{
                    'font': []
                }],
                ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                ['blockquote', 'code-block'],

                [{
                    'header': 1
                }, {
                    'header': 2
                }], // custom button values
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],

                [{
                    'color': []
                }, {
                    'background': []
                }], // dropdown with defaults from theme
                [{
                    'align': []
                }],

                ['image', 'video'],
                ['clean'] // remove formatting button
            ];

            var quill = new Quill('#mail-reply-editor', {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
            });
        });
    </script>
@endpush
