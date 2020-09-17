<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_feeds', function (Blueprint $table) {
            $table->dropForeign(['document_id']);
            $table->dropForeign(['feed_id']);

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade')->change();
            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade')->change();
        });

        Schema::table('feed_feed_items', function (Blueprint $table) {
            $table->dropForeign(['feed_id']);
            $table->dropForeign(['feed_item_id']);

            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade')->change();
            $table->foreign('feed_item_id')->references('id')->on('feed_items')->onDelete('cascade')->change();
        });

        Schema::table('feed_item_states', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['folder_id']);
            $table->dropForeign(['document_id']);
            $table->dropForeign(['feed_id']);
            $table->dropForeign(['feed_item_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade')->change();
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade')->change();
            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade')->change();
            $table->foreign('feed_item_id')->references('id')->on('feed_items')->onDelete('cascade')->change();
        });

        Schema::table('ignored_feeds', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['feed_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
