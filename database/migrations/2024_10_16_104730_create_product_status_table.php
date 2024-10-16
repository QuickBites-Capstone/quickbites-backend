<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStatusTable extends Migration
{
    public function up()
    {
        Schema::create('product_status', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_status');
    }
}