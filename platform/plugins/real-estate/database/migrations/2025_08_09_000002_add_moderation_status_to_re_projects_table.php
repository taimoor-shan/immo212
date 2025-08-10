<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('re_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('re_projects', 'moderation_status')) {
                $table->string('moderation_status', 60)->default('pending')->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('re_projects', function (Blueprint $table) {
            if (Schema::hasColumn('re_projects', 'moderation_status')) {
                $table->dropColumn('moderation_status');
            }
        });
    }
};
