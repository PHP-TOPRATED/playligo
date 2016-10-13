<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePlaylistRatingsRemoveRating extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('playlist_ratings', function (Blueprint $table) {
            $table->dropColumn('plr_rating');
            $table->dropColumn('plr_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('playlist_ratings', function (Blueprint $table) {
            $table->decimal('plr_rating', 3, 1)->default(0);
            $table->string('plr_status')->default('active');
        });
    }
}
