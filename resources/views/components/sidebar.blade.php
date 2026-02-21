<style>
    #sidebar-scroll .version-meta-item {
        position: sticky;
        bottom: 0.5rem;
        z-index: 1;
        padding-top: 0.75rem;
    }

    #sidebar-scroll .version-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 12px;
        line-height: 1;
        letter-spacing: 0.02em;
        padding: 0.35rem 0px;
        color: var(--text-muted, #8c9097);
    }

    #sidebar-scroll .version-badge .version-prefix {
        font-weight: 500;
        opacity: 0.85;
    }
</style>
<div class="main-sidebar" id="sidebar-scroll">
    <nav class="main-menu-container nav nav-pills flex-column sub-open">
        <div class="slide-left" id="slide-left">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
            </svg>
        </div>

        <ul class="main-menu">
            @foreach ($menuItems as $category)
                <li class="slide__category">
                    <span class="category-name">{{ $category['category'] }}</span>
                </li>

                @foreach ($category['items'] as $item)
                    <x-menu-item :item="$item" />
                @endforeach
            @endforeach

            <li class="slide__category version-meta-item">
                <span class="version-badge">
                    <span class="version-prefix">v</span>0.2.2
                </span>
            </li>
        </ul>

        <div class="slide-right" id="slide-right">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
            </svg>
        </div>
    </nav>
</div>
