<!-- resources/views/partials/menu.blade.php -->
{{-- <div class="menu-div">
    <a href="#">Parameter Menus</a> --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <ul class="menu navbar-nav me-auto mb-2 mb-lg-0">
            @foreach (settingsMenus() as $menu)
                <li class="menu-item nav-item">
                    <a href="{{ $menu->route ? route($menu->route) : '#' }}">{{ $menu->title }}</a>
                    @if ($menu->children->isNotEmpty())
                        <ul class="submenu">
                            @foreach ($menu->children as $submenu)
                                <li class="submenu-item nav-item">
                                    <a href="{{ $submenu->route ? route($submenu->route) : '#' }}">{{ $submenu->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
{{-- </div> --}}

<style>
/* Add some basic styling */
.menu {
    list-style-type: none;
    /* padding: 0;
    margin: 0;
    margin: 0;
    width: 200px; Adjust as needed */
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px; */
    /* display: none;
    /* overflow: hidden; */
}

.menu-item {
    position: relative;
}

.menu-item > a {
    display: block;
    padding: 10px;
    background-color: #f8f9fa;
    text-decoration: none;
    color: #000;
    border-right: 1px solid #ddd;
}

.menu-item > a:hover {
    background-color: #e9ecef;
}

.submenu {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: none; /* Initially hidden */
    position: absolute;
    left: 100%;
    top: 10;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    z-index: 100000;
    max-width: 500px;
    min-width: 300px;
}

.submenu-item > a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #000;
}

.submenu-item > a:hover {
    background-color: #e9ecef;
}

.menu-item:hover > .submenu {
    display: block; /* Show submenu on hover */
}

/* Optional: Style the submenu icon */
.menu-item > a::after {
    content: '▶';
    float: right;
    font-size: 12px;
    transition: transform 0.3s ease;
}

.menu-item:hover > a::after {
    transform: rotate(90deg);
}

/* .menu-div:hover > .menu {
    display: block; /* Show submenu on hover */
/* } */
/* .menu-div >a::after {
    content: '▶';
    float: right;
    font-size: 12px;
} */ */
</style>
