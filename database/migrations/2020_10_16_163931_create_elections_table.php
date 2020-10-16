<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //http://eldaddp.azurewebsites.net/electionresults.json?electionId=1167964
        //http://lda.data.parliament.uk/electionresults.json?electionId=1167964
        // http://lda.data.parliament.uk/electionresults.xml?_pageSize=650&electionId=1167964&_page=0
        
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('election');
            $table->date('date');
            $table->timestamps();
        });
        // Schema::create('elections', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('elections');
    }
}
