<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('genre')->nullable();
            $table->text('description');
            $table->string('img');
            // Stock is every copy the shop owns; number is the copies not sold or rented out.
            $table->unsignedInteger('number');
            $table->unsignedInteger('stock');
            $table->decimal('price', 8, 2)->default(0);
            // What renting one copy costs per month.
            $table->decimal('rentPrice', 8, 2)->default(0);
            // Copies free to rent right now.
            $table->unsignedInteger('forRent')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
