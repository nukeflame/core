<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themeSettings = [
            'theme.mode' => 'light',
            'theme.nav_layout' => 'vertical',
            'theme.vertical_style' => 'overlay',
            'theme.header_style' => 'light',
            'theme.menu_style' => 'light',
            'theme.primary_color' => '#f91520',
            'theme.secondary_color' => '#4d4f51',
        ];

        $candidateTables = [
            'settings',
            'system_settings',
            'general_settings',
            'theme_settings',
            'themes',
        ];

        foreach ($candidateTables as $table) {
            $this->seedKeyValueTable($table, $themeSettings);
        }

        $this->seedCompaniesThemeDefaults();
    }

    private function seedKeyValueTable(string $table, array $settings): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $columns = Schema::getColumnListing($table);

        $keyColumn = $this->firstAvailable($columns, ['key', 'name', 'setting_key', 'config_key', 'code']);
        $valueColumn = $this->firstAvailable($columns, ['value', 'setting_value', 'config_value', 'setting', 'description']);

        if (!$keyColumn || !$valueColumn) {
            return;
        }

        foreach ($settings as $key => $value) {
            $where = [$keyColumn => $key];
            $payload = [$valueColumn => $value];

            if (in_array('updated_by', $columns, true)) {
                $payload['updated_by'] = 'seeder';
            }
            if (in_array('created_by', $columns, true)) {
                $payload['created_by'] = 'seeder';
            }
            if (in_array('status', $columns, true) && !isset($payload['status'])) {
                $payload['status'] = 'A';
            }
            if (in_array('updated_at', $columns, true)) {
                $payload['updated_at'] = now();
            }
            if (in_array('created_at', $columns, true)) {
                $payload['created_at'] = now();
            }

            DB::table($table)->updateOrInsert($where, $payload);
        }
    }

    private function seedCompaniesThemeDefaults(): void
    {
        if (!Schema::hasTable('companies')) {
            return;
        }

        $columns = Schema::getColumnListing('companies');

        $updates = [];
        $mapping = [
            'theme_mode' => 'light',
            'nav_layout' => 'vertical',
            'vertical_style' => 'overlay',
            'header_style' => 'light',
            'menu_style' => 'light',
            'primary_color' => '#f91520',
            'secondary_color' => '#4d4f51',
        ];

        foreach ($mapping as $column => $value) {
            if (in_array($column, $columns, true)) {
                $updates[$column] = $value;
            }
        }

        if (empty($updates)) {
            return;
        }

        if (in_array('updated_at', $columns, true)) {
            $updates['updated_at'] = now();
        }

        if (in_array('company_id', $columns, true)) {
            DB::table('companies')->where('company_id', 1)->update($updates);
        } else {
            DB::table('companies')->limit(1)->update($updates);
        }
    }

    private function firstAvailable(array $columns, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return null;
    }
}
