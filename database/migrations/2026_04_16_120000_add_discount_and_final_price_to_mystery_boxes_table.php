<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mystery_boxes', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percentage')->default(0)->after('price');
            $table->decimal('final_price', 10, 2)->default(0)->after('discount_percentage');
        });

        DB::table('mystery_boxes')->update([
            'discount_percentage' => 0,
            'final_price' => DB::raw('price'),
        ]);
    }

    public function down(): void
    {
        Schema::table('mystery_boxes', function (Blueprint $table) {
            $table->dropColumn(['discount_percentage', 'final_price']);
        });
    }
};
