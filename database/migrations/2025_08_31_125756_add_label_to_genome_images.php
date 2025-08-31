<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLabelToGenomeImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('genome_images', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->string('title', 100)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('genome_images', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->string('name', 64)->change();
        });
    }
}
