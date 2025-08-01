<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('performance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('account_period');
            $table->date('record_date');

            $table->decimal('new_fac_gwp', 15, 2)->default(0);
            $table->decimal('new_special_gwp', 15, 2)->default(0);
            $table->decimal('new_treaty_gwp', 15, 2)->default(0);
            $table->decimal('new_market_gwp', 15, 2)->default(0);
            $table->decimal('new_fac_income', 15, 2)->default(0);
            $table->decimal('new_special_income', 15, 2)->default(0);
            $table->decimal('new_treaty_income', 15, 2)->default(0);
            $table->decimal('new_market_income', 15, 2)->default(0);

            $table->decimal('renewal_fac_gwp', 15, 2)->default(0);
            $table->decimal('renewal_special_gwp', 15, 2)->default(0);
            $table->decimal('renewal_treaty_gwp', 15, 2)->default(0);
            $table->decimal('renewal_market_gwp', 15, 2)->default(0);
            $table->decimal('renewal_fac_income', 15, 2)->default(0);
            $table->decimal('renewal_special_income', 15, 2)->default(0);
            $table->decimal('renewal_treaty_income', 15, 2)->default(0);
            $table->decimal('renewal_market_income', 15, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'account_period', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_records');
    }
};
