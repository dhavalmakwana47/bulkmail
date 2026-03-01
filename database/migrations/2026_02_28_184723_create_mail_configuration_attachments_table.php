<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_configuration_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_configuration_id')->constrained('mail_configurations')->onDelete('cascade');
            $table->foreignId('debtor_attachment_id')->constrained('debtor_attachments')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('mail_configuration_id');
            $table->index('debtor_attachment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_configuration_attachments');
    }
};
