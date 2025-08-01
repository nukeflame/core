<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemProcessPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_process_permissions', function (Blueprint $table) {
            $table->foreignId('system_process_id')
                ->constrained('system_process')
                ->onDelete('cascade');

            $table->foreignId('permission_id')
                ->constrained('permissions')
                ->onDelete('cascade');

            $table->primary(['system_process_id', 'permission_id']);

            $table->timestamps();
            $table->index(['system_process_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_process_permissions');
    }
}
