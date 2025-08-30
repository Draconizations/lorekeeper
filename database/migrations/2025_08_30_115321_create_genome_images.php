<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenomeImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genome_images', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->boolean('is_visible')->default(false);
        });

        Schema::create('image_locis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained('genome_images');
            $table->foreignId('allele_id')->nullable()->default(null)->constrained('loci_alleles');
            $table->foreignId('loci_id')->constrained('locis');
            $table->integer('position');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_locis');
        Schema::dropIfExists('genome_images');
    }
}
