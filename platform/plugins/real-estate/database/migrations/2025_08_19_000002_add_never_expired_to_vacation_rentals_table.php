<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('re_vacation_rentals', function (Blueprint $table) {
            if (! Schema::hasColumn('re_vacation_rentals', 'never_expired')) {
                $table->boolean('never_expired')->default(false)->after('auto_renew');
            }
        });
    }

    public function down(): void
    {
        Schema::table('re_vacation_rentals', function (Blueprint $table) {
            if (Schema::hasColumn('re_vacation_rentals', 'never_expired')) {
                $table->dropColumn('never_expired');
            }
        });
    }
};
