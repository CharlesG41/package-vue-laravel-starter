<?php

use Cyvian\Src\app\Classes\Action;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->enum('action_type', [Action::ACTION_TYPE_VIEW, Action::ACTION_TYPE_EXECUTE, Action::ACTION_TYPE_DOWNLOAD, Action::ACTION_TYPE_ADMIN]);
            $table->string('url')->nullable();
            $table->boolean('roles_by_entry');

            $table->foreignId('entry_type_id')
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
        Schema::dropIfExists('actions');
    }
}
