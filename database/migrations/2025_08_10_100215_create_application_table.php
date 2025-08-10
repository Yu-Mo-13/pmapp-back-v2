<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->after('id')->comment('アプリケーション名');
            $table->boolean('account_class')->after('name')->comment('アカウント区分');
            $table->boolean('notice_class')->after('account_class')->comment('変更通知区分');
            $table->boolean('mark_class')->after('notice_class')->comment('記号区分');
            $table->integer('pre_password_size')->after('mark_class')->comment('仮登録パスワード桁数');
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
        Schema::dropIfExists('applications');
    }
}
