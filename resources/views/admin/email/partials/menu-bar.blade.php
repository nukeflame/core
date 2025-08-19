<div class="menu-bar">
    @foreach (['File', 'Edit', 'View', 'Go', 'Message', 'Tools', 'Help'] as $menuItem)
        <div class="menu-item">{{ $menuItem }}</div>
    @endforeach
</div>
