<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public $menuItems;
    public $showReportSidebar;
    private $user;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->showReportSidebar = Cookie::has('show_report_sidebar');
        $this->user = User::find(auth()->user()->id);
        $userId = $this->user->id;

        // Create a cache key for this specific user
        $cacheKey = "sidebar_menu_user_{$userId}";

        // $this->menuItems = Cache::tags(['sidebar_menu', "user_{$userId}"])->remember($cacheKey, now()->addMinutes(30), function () {
        //     return $this->filterMenuByPermissions(
        //         $this->showReportSidebar ?
        //             config('report_sidebar.menu_items') :
        //             config('sidebar.menu_items')
        //     );
        // });
        $this->menuItems = $this->filterMenuByPermissions(
            $this->showReportSidebar ?
                config('report_sidebar.menu_items') :
                config('sidebar.menu_items')
        );
    }

    private function filterMenuByPermissions($menuItems)
    {
        $result = [];
        foreach ($menuItems as $category) {
            if (
                isset($category['visibility_check']) &&
                !$this->user->hasAnyPermission($category['visibility_check'])
            ) {
                continue;
            }
            $filteredItems = $this->filterMenuItems($category['items']);

            if (!empty($filteredItems)) {
                $category['items'] = $filteredItems;
                $result[] = $category;
            }
        }

        return $result;
    }

    private function filterMenuItems($items)
    {
        $result = [];
        foreach ($items as $item) {
            if (isset($item['permission']) && !$this->user->can($item['permission'])) {
                continue;
            }
            if (isset($item['submenu'])) {
                $filteredSubmenu = $this->filterSubmenuItems($item['submenu']);

                if (!empty($filteredSubmenu)) {
                    $item['submenu'] = $filteredSubmenu;
                    $result[] = $item;
                }
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }

    private function filterSubmenuItems($submenuItems)
    {
        $result = [];

        foreach ($submenuItems as $item) {
            if (isset($item['permission']) && !$this->user->can($item['permission'])) {
                continue;
            }

            if (isset($item['has_sub']) && $item['has_sub'] && isset($item['children'])) {
                $filteredChildren = $this->filterChildrenItems($item['children']);

                if (!empty($filteredChildren)) {
                    $item['children'] = $filteredChildren;
                    $result[] = $item;
                }
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }

    private function filterChildrenItems($childrenItems)
    {
        $result = [];

        foreach ($childrenItems as $item) {
            if (isset($item['permission']) && !$this->user->can($item['permission'])) {
                continue;
            }

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }
}
