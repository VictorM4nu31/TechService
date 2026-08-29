<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('created_by');
            $table->index('assigned_to');
            $table->index('due_date');
            $table->index('status');
            $table->index('category');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index('ticket_id');
            $table->index('user_id');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->index('ticket_id');
            $table->index('user_id');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropIndex(['assigned_to']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['category']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['ticket_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['ticket_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['client_id']);
        });
    }
};
