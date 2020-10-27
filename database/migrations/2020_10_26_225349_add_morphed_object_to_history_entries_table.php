<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMorphedObjectToHistoryEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history_entries', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropForeign(['document_id']);
            $table->dropForeign(['feed_id']);

            $table->dropColumn([
                'folder_id',
                'document_id',
                'feed_id'
            ]);

            $table->nullableMorphs('morphable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('history_entries', function (Blueprint $table) {
            $table->dropMorphs('morphable');

            $table->foreignId('folder_id')->nullable()->default(null)->constrained('folders')->onDelete('set null');
            $table->foreignId('document_id')->nullable()->default(null)->constrained('documents')->onDelete('set null');
            $table->foreignId('feed_id')->nullable()->default(null)->constrained('feeds')->onDelete('set null');
        });
    }
}
