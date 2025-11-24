<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
            $table->string('sequence_name', 50)->comment('Name of the sequence (e.g., cover_number, endorsement_number)');
            $table->string('prefix', 10)->nullable()->comment('Optional prefix for formatted output (e.g., C, EN)');
            $table->unsignedBigInteger('current_value')->default(0)->comment('Current sequence value');
            $table->unsignedInteger('year')->nullable()->comment('Year for year-based sequences, NULL for global sequences');
            $table->string('created_by', 50)->nullable()->comment('Username who created the sequence');
            $table->string('updated_by', 50)->nullable()->comment('Username who last updated the sequence');
            $table->text('notes')->nullable()->comment('Optional notes about the sequence');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['sequence_name', 'year'], 'sequences_name_year_unique');
            $table->index(['sequence_name', 'year'], 'sequences_name_year_idx');
            $table->index('sequence_name', 'sequences_name_idx');
            $table->index('year', 'sequences_year_idx');

            $table->index('created_by', 'sequences_created_by_idx');
            $table->index('updated_by', 'sequences_updated_by_idx');
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("COMMENT ON TABLE sequences IS 'Manages sequential number generation for covers, endorsements, and documents'");
        }

        $currentYear = Carbon::now()->year;

        DB::table('sequences')->insert([
            [
                'sequence_name' => 'cover_number',
                'prefix' => 'C',
                'current_value' => 0,
                'year' => $currentYear,
                'notes' => 'Cover number sequence initialized on ' . now()->toDateTimeString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sequence_name' => 'endorsement_number',
                'prefix' => null,
                'current_value' => 0,
                'year' => $currentYear,
                'notes' => 'Endorsement number sequence initialized on ' . now()->toDateTimeString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sequence_name' => 'document_number',
                'prefix' => 'EN',
                'current_value' => 305,
                'year' => null,
                'notes' => 'Document number sequence initialized on ' . now()->toDateTimeString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequences');
    }
};
