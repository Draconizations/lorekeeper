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

        Schema::create('image_alleles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained('genome_images');
            $table->foreignId('allele_id')->constrained('loci_alleles');
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
        Schema::dropIfExists('genome_images');
        Schema::dropIfExists('image_alleles');
    }
}
