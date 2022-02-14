<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudioParentAudioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio_parent_audio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audio_id');
            $table->unsignedBigInteger('audio_parent_id');

            $table->foreign('audio_id')
                ->references('id')
                ->on('audios')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('audio_parent_id')
                ->references('id')
                ->on('audio_parents')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audio_parent_audio');
    }
}
