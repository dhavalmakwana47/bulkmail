<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_recipient_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_configuration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->text('error_message')->nullable();
            $table->text('bounce_reason')->nullable();
            $table->string('message_id')->nullable();
            $table->timestamps();
            
            $table->index('mail_configuration_id');
            $table->index('contact_id');
            $table->index('status');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_recipient_logs');
    }
};
