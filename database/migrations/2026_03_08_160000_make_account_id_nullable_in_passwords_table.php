<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MakeAccountIdNullableInPasswordsTable extends Migration
{
    public function up()
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::create('passwords_tmp', function (Blueprint $table) {
                $table->id();
                $table->string('password')->comment('パスワード');
                $table->unsignedBigInteger('application_id')->comment('アプリケーションID');
                $table->unsignedBigInteger('account_id')->nullable()->comment('アカウントID');
                $table->timestamps();

                $table->index('application_id');
                $table->index('account_id');
            });

            DB::statement('
                INSERT INTO passwords_tmp (id, password, application_id, account_id, created_at, updated_at)
                SELECT id, password, application_id, account_id, created_at, updated_at
                FROM passwords
            ');

            Schema::drop('passwords');
            Schema::rename('passwords_tmp', 'passwords');
            return;
        }

        DB::statement('ALTER TABLE passwords ALTER COLUMN account_id DROP NOT NULL');
    }

    public function down()
    {
        DB::table('passwords')->whereNull('account_id')->update(['account_id' => 0]);

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::create('passwords_tmp', function (Blueprint $table) {
                $table->id();
                $table->string('password')->comment('パスワード');
                $table->unsignedBigInteger('application_id')->comment('アプリケーションID');
                $table->unsignedBigInteger('account_id')->comment('アカウントID');
                $table->timestamps();

                $table->index('application_id');
                $table->index('account_id');
            });

            DB::statement('
                INSERT INTO passwords_tmp (id, password, application_id, account_id, created_at, updated_at)
                SELECT id, password, application_id, account_id, created_at, updated_at
                FROM passwords
            ');

            Schema::drop('passwords');
            Schema::rename('passwords_tmp', 'passwords');
            return;
        }

        DB::statement('ALTER TABLE passwords ALTER COLUMN account_id SET NOT NULL');
    }
}
