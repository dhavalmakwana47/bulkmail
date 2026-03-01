<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ses_connections', function (Blueprint $table) {
            $table->id();
            $table->string('ses_name')->nullable();
            $table->string('username');
            $table->string('password');
            $table->string('region');
            $table->string('hostname');
            $table->integer('port');
            $table->enum('active', ['Y', 'N'])->default('N');
            $table->string('from_email', 45)->nullable();
            $table->timestamps();
            
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ses_connections');
    }
};
