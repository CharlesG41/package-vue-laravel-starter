<?php

use Cyvian\Src\App\Models\Cyvian\FieldValue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_values', function (Blueprint $table) {
            $table->id();
            $table->text('value')->nullable();

            $table->foreignId('field_id')
                ->nullable()
                ->constrained('fields')
                ->onDelete('cascade');

            $table->foreignId('field_value_id')
                ->nullable()
                ->constrained('field_values')
                ->onDelete('cascade');

            $table->foreignId('entry_id')
                ->constrained('entries')
                ->onDelete('cascade');

            $table->foreignId('locale_id')
                ->nullable()
                ->constrained('locales')
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
        Schema::dropIfExists('field_values');
    }
}
