<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDetailsInHistoryEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history_entries', function (Blueprint $table) {
            $table->dropColumn('details');

            $table->json('properties')->nullable()->default(null);
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
            $table->dropColumn([
                'properties'
            ]);

            $table->json('details')->nullable()->default(null);
        });
    }
}
