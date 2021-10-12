<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNovaPoshtaWarehousesTable extends Migration
{
    public function up()
    {
        Schema::create('nova_poshta_warehouses', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->uuid('ref')->unique()->index()->comment('UUID отделения');
            $table->uuid('type_ref')->index()->comment('UUID типа (почтомат, отделение ...)');

            $table->uuid('city_ref')->index();

            $table->string('description_ru')->comment('полное название и адресом');
            $table->string('description_uk');
            $table->string('short_address_ru')->comment('краткий адрес');
            $table->string('short_address_uk');

            $table->string('city_ru')->nullable();
            $table->string('city_uk')->nullable();
            $table->string('region')->nullable()->comment('Область');
            $table->string('city_type_ru')->nullable();
            $table->string('city_type_uk')->nullable();

            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();

            $table->string('phone', 12)->comment('Номер телефона');
            $table->string('number')->comment('Номер отделения');
            $table->unsignedBigInteger('max_weight')->default(0)->comment('Максимальный вес посылки');
            $table->boolean('pos_terminal')->default(false)->comment('POS Терминал в отделении');
            $table->boolean('postomat')->default(false)->comment('Почтомат');

            $table->boolean('active')->default(true)->comment('Доступно');

        });
    }

    public function down()
    {
        Schema::dropIfExists('nova_poshta_warehouses');
    }
}
