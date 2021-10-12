<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Области Новой Почты
class CreateNovaPoshtaRegionsTable extends Migration
{
    public function up()
    {
        Schema::create('nova_poshta_regions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('ref')->unique()->index();
            $table->uuid('areas_center')->index();
            $table->string('description_ru')->index();
            $table->string('description_uk');
            $table->boolean('active')->default(true);

        });
    }

    public function down()
    {
        Schema::dropIfExists('nova_poshta_regions');
    }
}
