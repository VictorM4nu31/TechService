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
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('impact')->default('Medio')->after('priority');
            $table->string('urgency')->default('Medio')->after('impact');
            $table->timestamp('sla_due_at')->nullable()->after('due_date');
            $table->index(['status', 'sla_due_at']);
            $table->index(['impact', 'urgency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['status', 'sla_due_at']);
            $table->dropIndex(['impact', 'urgency']);
            $table->dropColumn(['impact', 'urgency', 'sla_due_at']);
        });
    }
};
