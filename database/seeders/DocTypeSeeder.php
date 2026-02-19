<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DocTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (!Schema::hasTable('doc_types')) {
            return;
        }

        $columns = Schema::getColumnListing('doc_types');
        if (!in_array('code', $columns, true) || !in_array('doc_type', $columns, true)) {
            return;
        }

        $now = Carbon::now();

        $documents = [
            [
                'code' => 'CR12',
                'doc_type' => 'Company Registry Extract',
                'description' => 'Latest company registry extract for due diligence.',
                'country' => 'Kenya',
                'is_required' => 'Y',
                'is_default' => 'Y',
                'file_name' => 'uploads/cedant_docs/cr12_template.pdf',
                'mimetype' => 'application/pdf',
            ],
            [
                'code' => 'COI',
                'doc_type' => 'Certificate of Incorporation',
                'description' => 'Certificate of incorporation for client legal existence.',
                'country' => 'All',
                'is_required' => 'Y',
                'is_default' => 'Y',
                'file_name' => null,
                'mimetype' => null,
            ],
            [
                'code' => 'PROP_RE',
                'doc_type' => 'Reinsurance Proposal Form',
                'description' => 'Standard reinsurance proposal form template.',
                'country' => 'All',
                'is_required' => 'Y',
                'is_default' => 'Y',
                'file_name' => 'uploads/cedant_docs/reinsurance_proposal_form.pdf',
                'mimetype' => 'application/pdf',
            ],
            [
                'code' => 'FS3Y',
                'doc_type' => 'Audited Financial Statements',
                'description' => 'Last 3 years audited financial statements.',
                'country' => 'All',
                'is_required' => 'Y',
                'is_default' => 'N',
                'file_name' => null,
                'mimetype' => null,
            ],
            [
                'code' => 'RISK_SCHED',
                'doc_type' => 'Risk Schedule',
                'description' => 'Risk schedule detailing all insured items and values.',
                'country' => 'All',
                'is_required' => 'Y',
                'is_default' => 'Y',
                'file_name' => null,
                'mimetype' => null,
            ],
            [
                'code' => 'CLAIMS_5Y',
                'doc_type' => 'Claims Experience (5 Years)',
                'description' => 'Five-year claims history for underwriting assessment.',
                'country' => 'All',
                'is_required' => 'N',
                'is_default' => 'N',
                'file_name' => null,
                'mimetype' => null,
            ],
        ];

        $payload = collect($documents)->map(function ($row) use ($columns, $now) {
            $base = [
                'code' => $row['code'],
                'doc_type' => $row['doc_type'],
                'description' => $row['description'],
                'country' => $row['country'],
                'is_required' => $row['is_required'],
                'is_default' => $row['is_default'],
                'file_name' => $row['file_name'],
                'mimetype' => $row['mimetype'],
                'updated_at' => $now,
            ];

            if (in_array('attachment_file', $columns, true)) {
                $base['attachment_file'] = $row['file_name'] ? 'Y' : 'N';
            }
            if (in_array('checkbox_doc', $columns, true)) {
                $base['checkbox_doc'] = null;
            }
            if (in_array('bus_type', $columns, true)) {
                $base['bus_type'] = '';
            }
            if (in_array('category_type', $columns, true)) {
                $base['category_type'] = 1;
            }

            return $base;
        })->all();

        foreach ($payload as $row) {
            DB::table('doc_types')->updateOrInsert(
                ['code' => $row['code']],
                array_merge(['created_at' => $now], $row)
            );
        }

        // Normalize/update codes based on current doc_types table data.
        $knownCodeMap = [
            'COMPANY REGISTRY EXTRACT' => 'CR12',
            'CERTIFICATE OF INCORPORATION' => 'COI',
            'REINSURANCE PROPOSAL FORM' => 'PROP_RE',
            'PROPOSAL FORM' => 'PROP_FORM',
            'QUOTATION TEMPLATE' => 'QT_TPL',
            'FINANCIALS' => 'FIN',
            'QUOTATION TERMS' => 'QT_TERMS',
            'POLICY SCHEDULE' => 'POL_SCHED',
            'SUM INSURED BREAKDOWN/WINSURED ITEMS' => 'SI_BREAKDOWN',
            'AUDITED FINANCIAL STATEMENTS' => 'FS3Y',
            'RISK SCHEDULE' => 'RISK_SCHED',
            'CLAIMS EXPERIENCE (5 YEARS)' => 'CLAIMS_5Y',
        ];

        $rows = DB::table('doc_types')->select('id', 'doc_type', 'code')->orderBy('id')->get();
        $usedCodes = [];

        foreach ($rows as $row) {
            $docTypeName = trim((string) ($row->doc_type ?? ''));
            if ($docTypeName === '') {
                continue;
            }

            $normalizedName = strtoupper($docTypeName);
            $targetCode = $knownCodeMap[$normalizedName] ?? null;

            if (!$targetCode) {
                // Derive code from doc_type text: uppercase snake-like short code.
                $slug = Str::upper(Str::snake(Str::limit($docTypeName, 60, '')));
                $targetCode = substr(preg_replace('/[^A-Z0-9_]/', '', $slug), 0, 16);
                $targetCode = trim($targetCode, '_');
            }

            if ($targetCode === '') {
                $targetCode = 'DOC_' . $row->id;
            }

            // Ensure uniqueness.
            $baseCode = $targetCode;
            $counter = 1;
            while (
                in_array($targetCode, $usedCodes, true) ||
                DB::table('doc_types')->where('code', $targetCode)->where('id', '!=', $row->id)->exists()
            ) {
                $suffix = '_' . $counter;
                $targetCode = substr($baseCode, 0, max(1, 16 - strlen($suffix))) . $suffix;
                $counter++;
            }

            DB::table('doc_types')
                ->where('id', $row->id)
                ->update([
                    'code' => $targetCode,
                    'updated_at' => $now,
                ]);

            $usedCodes[] = $targetCode;
        }
    }
}
