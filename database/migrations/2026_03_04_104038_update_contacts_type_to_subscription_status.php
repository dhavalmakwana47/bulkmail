<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing records to SUBSCRIBED
        DB::table('contacts')->update(['type' => 'SUBSCRIBED']);
        
        // Modify column to set default value
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('type')->default('SUBSCRIBED')->change();
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('type')->default(null)->change();
        });
    }
};
