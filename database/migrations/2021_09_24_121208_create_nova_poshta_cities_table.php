<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNovaPoshtaCitiesTable extends Migration
{
    public function up()
    {
        Schema::create('nova_poshta_cities', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->uuid('ref')->unique()->index();
            $table->uuid('area')->index();
            $table->unsignedBigInteger('city_id')->index();
            $table->string('description_ru')->index();
            $table->string('description_uk');
            $table->string('area_description_ru')->index();
            $table->string('area_description_uk');
            $table->string('type_ru');
            $table->string('type_uk');

        });
    }

    public function down()
    {
        Schema::dropIfExists('nova_poshta_cities');
    }
}
