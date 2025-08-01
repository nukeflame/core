<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderTasksDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_tasks_docs', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('task_id', 50)->nullable();
            $table->integer('doctype')->nullable();
            $table->text('document')->nullable();
            $table->string('filetype', 50)->nullable();
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provider_tasks_docs');
    }
}
