<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('ses_email_id')->nullable()->constrained('ses_verified_emails')->nullOnDelete()->after('duplicate_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ses_email_id']);
            $table->dropColumn('ses_email_id');
        });
    }
};
