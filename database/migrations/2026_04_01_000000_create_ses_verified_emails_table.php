<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ses_verified_emails', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->enum('active_status', ['Y', 'N'])->default('Y');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ses_verified_emails');
    }
};
