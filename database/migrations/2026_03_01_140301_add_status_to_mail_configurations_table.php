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
        Schema::table('mail_configurations', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('scheduled_at')->comment('0=Pending, 1=Processing');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('mail_configurations', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
