<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_translations', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('message')->nullable();
            $table->string('action_label')->nullable();

            $table->foreignId('parent_id')
                ->constrained('actions')
                ->onDelete('cascade');

            $table->foreignId('locale_id')
                ->constrained()
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
        Schema::dropIfExists('action_translations');
    }
}
