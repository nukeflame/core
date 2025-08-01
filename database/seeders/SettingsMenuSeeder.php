<?php

namespace Database\Seeders;

use App\Models\SettingsMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all routes
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            // return strpos($route->getName(), 'settings.') === 0;
            return strpos($route->uri(), 'settings') === 0 && in_array('GET', $route->methods());
        });

        // Seed the routes into the menus table
        foreach ($routes as $route) {
            $id = SettingsMenu::max('id') + 1;

            SettingsMenu::create([
                'id' => $id,
                'title' => ucwords(str_replace('.', ' ', str_replace('settings.', '', $route->getName()))),
                'route' => $route->getName(),
                'parent_id' => null, // or set to appropriate parent ID if necessary
                'created_by' => 'seeder',
            ]);
        }
    }
}
