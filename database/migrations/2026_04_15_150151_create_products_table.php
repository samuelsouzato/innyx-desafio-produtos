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
       Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name', 50);             // i. Nome (Máx 50)
        $table->string('description', 200);      // ii. Descrição (Máx 200)
        $table->double('price');                 // iii. Preço
        $table->date('expiration_date');         // iv. Data de validade
        $table->string('image')->unique();       // v. Imagem (nome único)
        
        // vi. Categoria relacionada (Chave Estrangeira)
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
