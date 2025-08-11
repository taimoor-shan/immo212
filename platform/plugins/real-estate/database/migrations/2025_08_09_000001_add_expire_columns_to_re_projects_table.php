<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('re_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('re_projects', 'expire_date')) {
                $table->date('expire_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('re_projects', 'never_expired')) {
                $table->boolean('never_expired')->default(false)->after('expire_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('re_projects', function (Blueprint $table) {
            if (Schema::hasColumn('re_projects', 'never_expired')) {
                $table->dropColumn('never_expired');
            }
            if (Schema::hasColumn('re_projects', 'expire_date')) {
                $table->dropColumn('expire_date');
            }
        });
    }
};
