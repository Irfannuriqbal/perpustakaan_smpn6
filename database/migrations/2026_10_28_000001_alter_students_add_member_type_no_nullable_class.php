<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            // Rename nis → no (nomor induk anggota dari CSV)
            $table->renameColumn('nis', 'no');
        });

        Schema::table('students', function (Blueprint $table): void {
            // Make class nullable (guru/staf tidak punya kelas)
            $table->string('class')->nullable()->change();
            // Add member_type: Siswa/i or Guru/Staf
            $table->string('member_type')->default('Siswa/i')->after('no');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->renameColumn('no', 'nis');
        });

        Schema::table('students', function (Blueprint $table): void {
            $table->string('class')->nullable(false)->change();
            $table->dropColumn('member_type');
        });
    }
};
