<div class="total-mails border">
    <div class="p-3 d-flex align-items-center border-bottom">
        <div class="me-3">
            <input class="form-check-input" type="checkbox" id="checkAll" value="" aria-label="...">
        </div>
        <div class="flex-fill">
            <h6 class="fw-semibold mb-0">All Mails</h6>
        </div>
        <button class="btn btn-icon btn-light me-1 d-lg-none d-block total-mails-close" data-bs-toggle="tooltip"
            data-bs-placement="top" data-bs-title="Close">
            <i class="ri-close-line"></i>
        </button>
        <div class="dropdown">
            <button class="btn btn-icon btn-light btn-wave waves-light" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="ti ti-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Recent</a></li>
                <li><a class="dropdown-item" href="#">Unread</a></li>
                <li><a class="dropdown-item" href="#">Mark All Read</a></li>
                <li><a class="dropdown-item" href="#">Spam</a></li>
                <li><a class="dropdown-item" href="#">Delete All</a></li>
            </ul>
        </div>
    </div>
    <div class="p-3 border-bottom">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0" placeholder="Search Email"
                aria-describedby="button-addon2">
            <button class="btn btn-light" type="button" id="button-addon2"><i
                    class="ri-search-line text-muted"></i></button>
        </div>
    </div>
    <div class="mail-messages" id="mail-messages">
        <ul class="list-unstyled mb-0 mail-messages-container">
            <li class="active">
                <div class="d-flex align-items-top">
                    <div class="me-3 mt-1">
                        <input class="form-check-input" type="checkbox" id="checkboxNoLabel2" value=""
                            aria-label="..." checked>
                    </div>
                    <div class="me-1 lh-1">
                        <span class="avatar avatar-md online me-2 avatar-rounded mail-msg-avatar">
                            <img src="/assets/images/faces/12.jpg" alt="">
                        </span>
                    </div>
                    <div class="flex-fill">
                        <a href="javascript:void(0);">
                            <p class="mb-1 fs-12">
                                S Jeremy <span class="float-end text-muted fw-normal fs-11">10:27AM</span>
                            </p>
                        </a>
                        <p class="mail-msg mb-0">
                            <span class="d-block mb-0 fw-semibold text-truncate">History of planets are
                                discovered yesterday.</span>
                            <span class="fs-11 text-muted text-wrap text-truncate">Lorem
                                ipsum dolor sit amet consectetur adipisicing elit
                                <button class="btn p-0 lh-1 mail-starred border-0 float-end">
                                    <i class="ri-star-fill fs-14"></i>
                                </button>
                            </span>
                        </p>
                    </div>
                </div>
            </li>

        </ul>
    </div>
</div>
<div class="mails-information border"></div>
