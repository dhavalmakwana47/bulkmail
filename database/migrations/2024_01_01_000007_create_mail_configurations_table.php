<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('from_name');
            $table->string('reply_email');
            $table->string('subject');
            $table->longText('body');
            $table->json('attachments')->nullable();
            $table->string('send_type');
            $table->dateTime('scheduled_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('send_type');
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_configurations');
    }
};
