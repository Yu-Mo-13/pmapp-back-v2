<?php

use App\Http\Enums\Role\RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTopPageUrlToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('top_page_url')->after('code')->comment('トップページURL');
            });

            return;
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->string('top_page_url')->nullable()->after('code')->comment('トップページURL');
        });

        DB::table('roles')
            ->where('code', RoleEnum::ADMIN)
            ->update(['top_page_url' => '/applications']);

        DB::table('roles')
            ->whereIn('code', [RoleEnum::WEB_USER, RoleEnum::MOBILE_USER])
            ->update(['top_page_url' => '/passwords']);

        DB::table('roles')
            ->whereNull('top_page_url')
            ->update(['top_page_url' => '/passwords']);

        DB::statement('ALTER TABLE roles ALTER COLUMN top_page_url SET NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('top_page_url');
        });
    }
}
