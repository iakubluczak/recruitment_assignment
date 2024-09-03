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
                $table->index('birthdate');
            });
    
            Schema::table('purchases', function (Blueprint $table) {
                $table->index('purchase_date');
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
                $table->dropIndex(['birthdate']);
            });
    
            Schema::table('purchases', function (Blueprint $table) {
                $table->dropIndex(['purchase_date']);
            });
        });
    }
};
