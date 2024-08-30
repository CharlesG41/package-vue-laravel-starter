<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionEntryRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_entry_role', function (Blueprint $table) {
            $table->foreignId('action_id')
                ->constrained()
                ->onDelete('cascade');

            $table->integer('entry_id');

            $table->foreignId('role_id')
                ->constrained('entries')
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
        Schema::dropIfExists('action_entry_role');
    }
}
