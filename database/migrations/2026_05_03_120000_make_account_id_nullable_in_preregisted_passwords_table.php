<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::create('preregisted_passwords_tmp', function (Blueprint $table) {
                $table->uuid('uuid')->comment('UUID');
                $table->string('password')->comment('パスワード');
                $table->unsignedBigInteger('application_id')->comment('アプリケーションID');
                $table->unsignedBigInteger('account_id')->nullable()->comment('アカウントID');
                $table->timestamps();

                $table->primary('uuid');
                $table->index('application_id');
                $table->index('account_id');
            });

            DB::statement('
                INSERT INTO preregisted_passwords_tmp (uuid, password, application_id, account_id, created_at, updated_at)
                SELECT uuid, password, application_id, account_id, created_at, updated_at
                FROM preregisted_passwords
            ');

            Schema::drop('preregisted_passwords');
            Schema::rename('preregisted_passwords_tmp', 'preregisted_passwords');
            return;
        }

        DB::statement('ALTER TABLE preregisted_passwords ALTER COLUMN account_id DROP NOT NULL');
    }

    public function down(): void
    {
        DB::table('preregisted_passwords')->whereNull('account_id')->delete();

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::create('preregisted_passwords_tmp', function (Blueprint $table) {
                $table->uuid('uuid')->comment('UUID');
                $table->string('password')->comment('パスワード');
                $table->unsignedBigInteger('application_id')->comment('アプリケーションID');
                $table->unsignedBigInteger('account_id')->comment('アカウントID');
                $table->timestamps();

                $table->primary('uuid');
                $table->index('application_id');
                $table->index('account_id');
            });

            DB::statement('
                INSERT INTO preregisted_passwords_tmp (uuid, password, application_id, account_id, created_at, updated_at)
                SELECT uuid, password, application_id, account_id, created_at, updated_at
                FROM preregisted_passwords
            ');

            Schema::drop('preregisted_passwords');
            Schema::rename('preregisted_passwords_tmp', 'preregisted_passwords');
            return;
        }

        DB::statement('ALTER TABLE preregisted_passwords ALTER COLUMN account_id SET NOT NULL');
    }
};
