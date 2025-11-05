<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'multiple_choice',
                'translate',
                'speak',
                'listen',
                'match_pairs',
                'fill_blank',
                'word_order'
            ]);
            $table->text('question');
            $table->string('question_audio')->nullable(); // audio file path
            $table->json('options')->nullable(); // for multiple choice
            $table->text('correct_answer');
            $table->string('correct_audio')->nullable(); // audio for correct answer
            $table->text('explanation')->nullable();
            $table->integer('xp_reward')->default(5);
            $table->integer('order')->default(0);
            $table->string('image')->nullable(); // optional image
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exercises');
    }
};
