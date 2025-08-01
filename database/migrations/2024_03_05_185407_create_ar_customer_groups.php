<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ar_customer_groups', function (Blueprint $table) {
            $table->string('group_id', 20)->nullable(false);
            $table->string('group_title', 100)->nullable(false);
            $table->string('group_description', 150)->nullable(false);
            $table->string('default_currency', 3)->nullable(false);
            $table->string('control_account', 100)->nullable(false);
            $table->string('tax_category', 5)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ar_customer_groups');
    }
};
