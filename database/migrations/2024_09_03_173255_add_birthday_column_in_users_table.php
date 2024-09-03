<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('users', function (Blueprint $table) {
                $table->date('birthday')->nullable();
            });
    
            DB::statement("UPDATE users SET birthday = strftime('%m-%d', birthdate)");
            
            Schema::table('users', function (Blueprint $table) {
                $table->index('birthday');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['birthday']);
            });
    
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('birthday');
            });
        });
    }
};
