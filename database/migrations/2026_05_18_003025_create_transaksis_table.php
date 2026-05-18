<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasir_id')->constrained('users');
            $table->string('no_transaksi')->unique();
            $table->decimal('total_harga', 15, 2);
            $table->decimal('jumlah_bayar', 15, 2);
            $table->decimal('kembalian', 15, 2);
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris']);
            $table->enum('status', ['selesai', 'dibatalkan'])->default('selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};