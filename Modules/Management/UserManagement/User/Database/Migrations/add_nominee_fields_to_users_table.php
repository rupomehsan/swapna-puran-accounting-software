<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nominee_name')) {
                $table->string('nominee_name', 100)->nullable()->after('join_date');
            }
            if (!Schema::hasColumn('users', 'nominee_relation')) {
                $table->string('nominee_relation', 50)->nullable()->after('nominee_name');
            }
            if (!Schema::hasColumn('users', 'nominee_nid')) {
                $table->string('nominee_nid', 50)->nullable()->after('nominee_relation');
            }
            if (!Schema::hasColumn('users', 'nominee_image')) {
                $table->string('nominee_image', 200)->nullable()->after('nominee_nid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['nominee_name', 'nominee_relation', 'nominee_nid', 'nominee_image'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
