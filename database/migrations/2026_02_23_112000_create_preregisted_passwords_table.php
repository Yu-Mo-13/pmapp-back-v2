<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreregistedPasswordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preregisted_passwords', function (Blueprint $table) {
            $table->uuid('uuid')->comment('UUID');
            $table->string('password')->comment('パスワード');
            $table->unsignedBigInteger('application_id')->comment('アプリケーションID');
            $table->unsignedBigInteger('account_id')->comment('アカウントID');
            $table->timestamps();

            $table->primary('uuid');
            $table->index('application_id');
            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preregisted_passwords');
    }
}
