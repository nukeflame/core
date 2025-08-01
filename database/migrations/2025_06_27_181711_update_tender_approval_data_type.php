<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTenderApprovalDataType extends Migration
{
    public function up()
    {
        // 1. Drop the foreign key constraint and the index
        Schema::table('tender_approvals', function (Blueprint $table) {
            $table->dropForeign(['approver_id']); // drop the FK
        });

        // 2. Change column type to JSON
        DB::statement('ALTER TABLE tender_approvals ALTER COLUMN approver_id TYPE JSON USING to_jsonb(ARRAY[approver_id])');
    }

    public function down()
    {
        // Revert to integer and restore the foreign key
        DB::statement('ALTER TABLE tender_approvals ALTER COLUMN approver_id TYPE BIGINT USING (approver_id::bigint)');

        Schema::table('tender_approvals', function (Blueprint $table) {
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
