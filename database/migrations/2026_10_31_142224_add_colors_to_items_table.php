<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. Items saved with a removed font move to Standard,
     * because a font missing from the ItemFont enum would crash on load.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('color_scheme')->default('white-on-black');
        });

        DB::table('items')
            ->whereNotIn('font', ['System', 'roboto-mono'])
            ->update(['font' => 'System']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('color_scheme');
        });
    }
};
