<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('type');
            $table->json('name');
            $table->json('description');
            $table->integer('width');
            $table->boolean('translatable');
            $table->boolean('display_on_list');
            $table->boolean('has_filter');
            $table->boolean('locked');
            $table->json('conditions');
            $table->boolean('hidden_on_create');
            $table->boolean('hidden_on_edit');
            $table->boolean('disabled_on_edit');
            $table->json('roles_on_create');
            $table->json('roles_on_edit_or_disable');
            $table->json('roles_on_edit_or_hide');
            $table->boolean('is_base_field');
            $table->integer('entry_id')->nullable();
            $table->integer('entity_id');
            $table->string('entity_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fields');
    }
}
