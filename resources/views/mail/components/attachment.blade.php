<div class="attachment-item d-flex align-items-center p-2 border rounded mb-2" data-attachment-id="{{ $attachment->id }}">
    <div class="attachment-icon me-2">
        <i class="{{ $attachment->icon_class }} fs-20"></i>
    </div>
    <div class="flex-fill">
        <div class="attachment-name fw-semibold">{{ $attachment->name }}</div>
        <div class="attachment-size text-muted fs-12">{{ $attachment->formatted_size }}</div>
    </div>
    <div class="attachment-actions">
        <button class="btn btn-sm btn-outline-primary me-1" onclick="downloadAttachment('{{ $attachment->id }}')">
            <i class="ri-download-line"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary" onclick="previewAttachment('{{ $attachment->id }}')">
            <i class="ri-eye-line"></i>
        </button>
    </div>
</div>
