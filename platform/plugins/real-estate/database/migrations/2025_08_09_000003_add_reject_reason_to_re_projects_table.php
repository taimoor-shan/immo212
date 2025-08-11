<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('re_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('re_projects', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('moderation_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('re_projects', function (Blueprint $table) {
            if (Schema::hasColumn('re_projects', 'reject_reason')) {
                $table->dropColumn('reject_reason');
            }
        });
    }
};
