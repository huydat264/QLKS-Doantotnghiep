<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('combo', 'active')) {
            Schema::table('combo', function (Blueprint $table) {
                $table->tinyInteger('active')->default(1)->after('dieu_khoan');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('combo', 'active')) {
            Schema::table('combo', function (Blueprint $table) {
                $table->dropColumn('active');
            });
        }
    }
};
