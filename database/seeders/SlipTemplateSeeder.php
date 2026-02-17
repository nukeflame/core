<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SlipTemplateSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!Schema::hasTable('slip_templates')) {
            return;
        }

        $templateColumns = Schema::getColumnListing('slip_templates');
        $hasColumn = fn(string $column): bool => in_array($column, $templateColumns, true);

        $groupQuery = DB::table('class_groups')->select(['group_code', 'group_name']);
        $groupQuery->where('status', 'A');
        $groups = $groupQuery->get();

        $classQuery = DB::table('classes')->select(['class_code', 'class_name', 'class_group_code']);
        $classQuery->where('status', 'A');
        $classes = $classQuery->get();

        $fireGroup = $groups->first(
            fn($item) => strtolower(trim((string) $item->group_name)) === 'fire'
        );

        if (!$fireGroup) {
            return;
        }

        $fireClasses = $classes->where('class_group_code', $fireGroup->group_code)->values();

        if ($fireClasses->isEmpty()) {
            return;
        }

        $now = now();

        $templateDefinitions = [
            [
                'schedule_title' => 'Policy Wording',
                'description' => 'Standard policy wording and interpretation clauses.',
                'status' => 'A',
                'type_of_bus' => 'FAC',
            ],
            [
                'schedule_title' => 'Claims Notification Provisions',
                'description' => 'claims',
                'status' => 'A',
                'type_of_bus' => 'FAC',
            ],
            [
                'schedule_title' => 'Premium Payment Terms',
                'description' => 'Premium due dates, payment mode, and default terms.',
                'status' => 'I',
                'type_of_bus' => 'FAC',
            ],
            [
                'schedule_title' => 'Reinsurers Liability Clause',
                'description' => 'Reinsurer liability is several and not joint; each reinsurer is responsible only for its written/signed line.',
                'status' => 'A',
                'type_of_bus' => 'TRT',
            ],
            [
                'schedule_title' => 'Extended Reporting Period',
                'description' => 'Provides an optional extended reporting period (typically 12 months) after non-renewal or organizational change, subject to policy conditions.',
                'status' => 'A',
                'type_of_bus' => 'TRT',
            ],
            [
                'schedule_title' => 'Retroactive Cover Clause',
                'description' => 'Applies cover to claims from negligent acts occurring on or after the specified retroactive date, subject to continuity and policy terms.',
                'status' => 'A',
                'type_of_bus' => 'TRT',
            ],
            [
                'schedule_title' => 'Law and Jurisdiction Clause',
                'description' => 'Contract governed by the law of the reinsured location with exclusive local jurisdiction.',
                'status' => 'A',
                'type_of_bus' => 'TRT',
            ],
            [
                'schedule_title' => 'Professional Instruments Clause',
                'description' => 'Condition precedent requiring proper handling, approved sterilization, and guideline compliance for professional tools and implements.',
                'status' => 'A',
                'type_of_bus' => 'TRT',
            ],
            [
                'schedule_title' => 'Cyber and Data Limited Exclusion',
                'description' => 'Excludes cyber acts/incidents and data-related losses except for specified ensuing bodily injury or physical property damage.',
                'status' => 'A',
                'type_of_bus' => 'TRT',
            ],
            [
                'schedule_title' => 'War and Terrorism Exclusion',
                'description' => 'Excludes losses directly or indirectly caused by war, terrorism, or related prevention/suppression actions.',
                'status' => 'A',
                'type_of_bus' => 'TRT',
            ],
        ];

        $rows = $fireClasses->flatMap(function ($class) use (
            $templateDefinitions,
            $fireGroup,
            $hasColumn,
            $now
        ) {
            return collect($templateDefinitions)->map(function (array $item) use (
                $class,
                $fireGroup,
                $now
            ) {
                $businessType = $item['type_of_bus'] ?? null;
                $templateTitle = (string) ($item['schedule_title'] ?? 'Policy Wording');
                $templateBody = (string) ($item['description'] ?? $templateTitle);
                $record = [];

                $record['schedule_title'] = $templateTitle;
                $record['title'] = $templateTitle;

                $record['clause_title'] = $templateTitle;

                $record['description'] = $templateBody;

                $record['wording'] = $templateBody;

                $record['clause_wording'] = $templateBody;

                $record['status'] = $item['status'] ?? 'A';

                $record['type_of_bus'] = $businessType;

                $record['treaty_type'] = $businessType;

                $record['class_group_code'] = $fireGroup->group_code;

                $record['class_code'] = $class->class_code ?? null;

                $record['class_group'] = $fireGroup->group_name;

                $record['class_name'] = $class->class_name ?? null;

                $record['created_at'] = $now;

                $record['updated_at'] = $now;

                $record['created_by'] = 'system';

                $record['updated_by'] = 'system';

                return $record;
            });
        })->map(function (array $row) use ($hasColumn) {
            return array_filter(
                $row,
                fn($value, $key) => $hasColumn((string) $key),
                ARRAY_FILTER_USE_BOTH
            );
        })->values()->all();

        $matchColumns = match (true) {
            $hasColumn('schedule_title') && $hasColumn('class_code') => ['schedule_title', 'class_code'],
            $hasColumn('schedule_title') && $hasColumn('class_name') => ['schedule_title', 'class_name'],
            $hasColumn('schedule_title') => ['schedule_title'],
            $hasColumn('class_code') && $hasColumn('class_group_code') => ['class_code', 'class_group_code'],
            $hasColumn('class_code') => ['class_code'],
            $hasColumn('class_group_code') => ['class_group_code'],
            $hasColumn('class_name') => ['class_name'],
            $hasColumn('class_group') => ['class_group'],
            default => [],
        };

        foreach ($rows as $row) {
            if (empty($matchColumns)) {
                DB::table('slip_templates')->insert($row);
                continue;
            }

            $match = [];
            foreach ($matchColumns as $column) {
                if (!array_key_exists($column, $row)) {
                    continue;
                }

                $value = $row[$column];
                if ($value === null || $value === '') {
                    continue;
                }

                $match[$column] = $value;
            }

            if (empty($match)) {
                DB::table('slip_templates')->insert($row);
                continue;
            }

            $updates = $row;
            foreach (array_keys($match) as $column) {
                unset($updates[$column]);
            }


            DB::table('slip_templates')->updateOrInsert($match, $updates);
        }
    }
}
