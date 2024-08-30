<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntryTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_type_translations', function (Blueprint $table) {
            $table->id();
            $table->string('singular_name');
            $table->string('plural_name');

            $table->foreignId('parent_id')
                ->constrained('entry_types')
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
        Schema::dropIfExists('entry_type_translations');
    }
}
