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
        Schema::create('sendemail_templates', function (Blueprint $table) {
            $table->string('template_id', 4);
            $table->string('template_name', 100);
            $table->longText('template_body');
            $table->timestamps();
            $table->string('created_by', 20);
            $table->string('updated_by', 20);
            $table->unique(['template_id'], 'sendemail_templates_1');
            $table->unique(['template_name'], 'sendemail_templates_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sendemail_templates');
    }
};
