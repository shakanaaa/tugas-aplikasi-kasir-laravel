<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk')->constrained('produk')->onDelete('cascade');
            $table->foreignId('dicatat_oleh')->constrained('users')->onDelete('cascade');
            $table->integer('jumlah');
            $table->decimal('harga_beli');
            $table->string('supplier');
            $table->string('keterangan');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('stock_masuk');
    }
    
};
