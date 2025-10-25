<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the existing auto-increment primary key
            $table->dropPrimary(['id']);
            
            // Change id to UUID
            $table->uuid('id')->primary()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop UUID primary key
            $table->dropPrimary(['id']);
            
            // Change back to auto-increment bigint
            $table->bigIncrements('id')->change();
        });
    }
};
