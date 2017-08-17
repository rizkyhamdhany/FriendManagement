<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relations', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->boolean('is_friend')->after('second_user_id');
            $table->boolean('subscribed')->after('is_friend');
            $table->boolean('blocked')->after('subscribed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relations', function (Blueprint $table) {
            $table->dropColumn('is_friend');
            $table->dropColumn('subscribed');
            $table->dropColumn('blocked');
            $table->boolean('status')->after('second_user_id');
        });
    }
}
