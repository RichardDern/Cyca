<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->unsignedSmallInteger('http_status_code')->nullable()->default(null);
            $table->string('mimetype')->nullable()->default(null);
            $table->text('title')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->text('favicon_path')->nullable()->default(null);
            $table->dateTimeTz('checked_at')->nullable()->default(null);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
