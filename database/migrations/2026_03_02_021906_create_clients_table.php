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
        Schema::create('clients', function (Blueprint $迫) {
            $迫->id();
            $迫->string('name');
            $迫->string('contact_email')->nullable();
            $迫->string('contact_phone')->nullable();
            $迫->text('address')->nullable();
            $迫->string('tax_id')->unique()->nullable();
            $迫->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
