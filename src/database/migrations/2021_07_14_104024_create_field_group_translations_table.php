<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldGroupTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_group_translations', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->foreignId('parent_id')
                ->constrained('field_groups')
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
        Schema::dropIfExists('field_group_translations');
    }
}
