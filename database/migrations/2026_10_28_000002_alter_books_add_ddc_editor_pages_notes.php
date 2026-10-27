<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->string('ddc', 30)->nullable()->after('isbn');
            $table->string('editor')->nullable()->after('author');
            $table->string('total_pages')->nullable()->after('publication_year');
            $table->text('notes')->nullable()->after('shelf_location');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->dropColumn(['ddc', 'editor', 'total_pages', 'notes']);
        });
    }
};
