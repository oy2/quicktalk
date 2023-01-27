<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            // chat name
            $table->string('name');

            $table->timestamps();
        });

        // update messages table to have conversation foreign key
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
        });

        // make table for User to Many Conversations
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn('conversation_id');
        });

        Schema::dropIfExists('conversation_user');
    }
};
