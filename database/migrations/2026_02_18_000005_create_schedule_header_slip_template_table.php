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
        if (!Schema::hasTable('schedule_header_slip_template')) {
            Schema::create('schedule_header_slip_template', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('slip_template_id');
                $table->unsignedBigInteger('schedule_header_id');
                $table->timestamps();

                $table->unique(['slip_template_id', 'schedule_header_id'], 'sh_st_unique');

                if (Schema::hasTable('slip_templates')) {
                    $table->foreign('slip_template_id', 'sh_st_slip_fk')
                        ->references('id')
                        ->on('slip_templates')
                        ->onDelete('cascade');
                }

                if (Schema::hasTable('quote_schedule_headers')) {
                    $table->foreign('schedule_header_id', 'sh_st_header_fk')
                        ->references('id')
                        ->on('quote_schedule_headers')
                        ->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_header_slip_template');
    }
};
