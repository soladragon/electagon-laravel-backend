<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //http://lda.data.parliament.uk/resources/1223004.json
        //http://lda.data.parliament.uk/resources/1223004/candidates/1.json
        //http://lda.data.parliament.uk/resources/1223004/candidates/6.xml
        //http://lda.data.parliament.uk/resources/1223004/candidates/6

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->interger('constituency_id')->unsigned();
            $table->string('full_name');
            $table->string('votes');
            $table->string('party');
            $table->interger('position');
            $table->string('result');
            $table->string('vote_change_percentage');
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
        Schema::dropIfExists('candidates');
    }
}
