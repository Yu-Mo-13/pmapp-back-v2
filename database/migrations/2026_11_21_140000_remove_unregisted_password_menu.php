<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            DB::table('menus')->where('path', '/unregisted-passwords')->delete();

            DB::table('menus')
                ->where('path', '/temp-passwords')
                ->where('sort_order', 5)
                ->update(['sort_order' => 4, 'updated_at' => now()]);
        });
    }

    public function down(): void
    {
        // The deleted menu's original settings cannot be reconstructed.
    }
};
