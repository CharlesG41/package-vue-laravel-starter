<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionEntryTypeRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_entry_type_role', function (Blueprint $table) {
            $table->foreignId('action_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('entry_type_id')
                ->constrained('entry_types')
                ->onDelete('cascade');

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
        Schema::dropIfExists('action_entry_type_role');
    }
}
