<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('status')->nullable()->after('category_id');
            $table->string('priority')->nullable()->after('status');
            $table->string('category')->nullable()->after('priority');
        });

        DB::statement('UPDATE tickets SET status = (SELECT name FROM statuses WHERE statuses.id = tickets.status_id)');
        DB::statement('UPDATE tickets SET priority = (SELECT name FROM priorities WHERE priorities.id = tickets.priority_id)');
        DB::statement('UPDATE tickets SET category = (SELECT name FROM categories WHERE categories.id = tickets.category_id)');

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('status')->default('Abierto')->change();
            $table->string('priority')->default('Media')->change();
            $table->string('category')->default('Correctivo')->change();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropForeign(['priority_id']);
            $table->dropForeign(['category_id']);
            $table->dropColumn(['status_id', 'priority_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedInteger('status_id')->nullable()->after('status');
            $table->unsignedInteger('priority_id')->nullable()->after('priority');
            $table->unsignedInteger('category_id')->nullable()->after('category');
        });

        DB::statement('UPDATE tickets SET status_id = (SELECT id FROM statuses WHERE statuses.name = tickets.status)');
        DB::statement('UPDATE tickets SET priority_id = (SELECT id FROM priorities WHERE priorities.name = tickets.priority)');
        DB::statement('UPDATE tickets SET category_id = (SELECT id FROM categories WHERE categories.name = tickets.category)');

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['status', 'priority', 'category']);
        });
    }
};
