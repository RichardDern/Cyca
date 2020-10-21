<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->default(null)->constrained('users')->onDelete('set null');
            $table->foreignId('folder_id')->nullable()->default(null)->constrained('folders')->onDelete('set null');
            $table->foreignId('document_id')->nullable()->default(null)->constrained('documents')->onDelete('set null');
            $table->foreignId('feed_id')->nullable()->default(null)->constrained('feeds')->onDelete('set null');
            $table->string('event');
            $table->json('details')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_entries');
    }
}
