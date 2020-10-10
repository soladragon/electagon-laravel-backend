<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConstituenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //http://eldaddp.azurewebsites.net/electionresults.json?electionId=1167964

        Schema::create('constituencies', function (Blueprint $table) {
            $table->id();
            $table->integer('election_id');
            $table->string('constituency');
            $table->integer('electorate');
            $table->integer('majority');
            $table->string('result');
            $table->string('turnout');
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
        Schema::dropIfExists('constituencies');
    }
}
