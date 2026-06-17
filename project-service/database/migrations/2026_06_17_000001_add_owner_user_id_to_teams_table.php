<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('teams', 'owner_user_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->unsignedBigInteger('owner_user_id')->nullable()->after('description')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('teams', 'owner_user_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropColumn('owner_user_id');
            });
        }
    }
};
