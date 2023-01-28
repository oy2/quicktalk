<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('conversation_user', function (Blueprint $table) {
            // create column called unread with true/false, default false
            $table->boolean('unread')->default(false);
        });
    }

    public function down()
    {
        Schema::table('conversation_user', function (Blueprint $table) {
            // drop column
            $table->dropColumn('unread');
        });
    }
};
