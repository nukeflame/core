<li class="slide {{ isset($item['submenu']) ? 'has-sub' : '' }}">
    @if (isset($item['submenu']))
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="{{ $item['icon'] ?? '' }} side-menu__icon"></i>
            <span class="side-menu__label">{{ $item['title'] }}</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">{{ $item['title'] }}</a>
            </li>

            @foreach ($item['submenu'] as $subItem)
                @if (isset($subItem['has_sub']) && $subItem['has_sub'])
                    <li class="slide has-sub">
                        <a href="javascript:void(0);" class="side-menu__item">
                            {{ $subItem['title'] }}
                            <i class="fe fe-chevron-right side-menu__angle"></i>
                        </a>
                        <ul class="slide-menu child2">
                            @foreach ($subItem['children'] as $child)
                                <li class="slide">
                                    <a href="{{ isset($child['route']) ? route($child['route']) : 'javascript:void(0);' }}"
                                        class="side-menu__item">
                                        {{ $child['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li class="slide">
                        <a href="{{ isset($subItem['route']) ? route($subItem['route']) : 'javascript:void(0);' }}"
                            data-url="{{ isset($subItem['route']) ? route($subItem['route']) : '' }}"
                            class="side-menu__item">
                            {{ $subItem['title'] }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    @else
        @if (isset($item['route']) && $item['route'] === '/')
            <a href="/" class="side-menu__item {{ $item['classes'] ?? '' }}">
                <i class="{{ $item['icon'] ?? '' }} side-menu__icon"></i>
                <span class="side-menu__label">{{ $item['title'] }}</span>
            </a>
        @else
            <a href="{{ isset($item['route']) ? (strpos($item['route'], 'http') === 0 ? $item['route'] : route($item['route'])) : 'javascript:void(0);' }}"
                class="side-menu__item {{ $item['classes'] ?? '' }}">
                <i class="{{ $item['icon'] ?? '' }} side-menu__icon"></i>
                <span class="side-menu__label">{{ $item['title'] }}</span>
            </a>
        @endif
    @endif
</li>
