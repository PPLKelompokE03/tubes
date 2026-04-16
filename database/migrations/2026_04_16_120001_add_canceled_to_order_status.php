<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','paid','completed','canceled') NOT NULL DEFAULT 'pending'");
        }

        // SQLite stores enums as varchar; no ALTER needed for new status values.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE orders SET status = 'completed' WHERE status = 'canceled'");
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','paid','completed') NOT NULL DEFAULT 'pending'");
        }
    }
};
