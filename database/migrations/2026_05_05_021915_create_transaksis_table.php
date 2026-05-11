<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasir_id')->constrained('users')->onDelete('restrict');
            $table->integer('total_harga');
            $table->integer('jumlah_bayar');
            $table->integer('kembalian');
            $table->string('status')->default('pending');
            $table->string('metode_pembayaran');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
