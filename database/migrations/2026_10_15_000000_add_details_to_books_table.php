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
        Schema::table('books', function (Blueprint $table) {
            $table->string('author')->nullable()->after('title');
            $table->string('genre')->nullable()->after('author');
            $table->decimal('price', 8, 2)->default(0)->after('stock');
            $table->unsignedInteger('forRent')->default(0)->after('price');
            $table->boolean('eBook')->default(false)->after('forRent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['author', 'genre', 'price', 'forRent', 'eBook']);
        });
    }
};
