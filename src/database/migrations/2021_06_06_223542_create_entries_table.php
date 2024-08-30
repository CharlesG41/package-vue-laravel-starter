<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Cyvian\Src\app\Classes\Entry;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->integer('order');
            $table->enum('status', [Entry::STATUS_PUBLISHED, Entry::STATUS_DRAFT, Entry::STATUS_SCHEDULED_PUBLICATION, Entry::STATUS_ARCHIVED]);
            $table->dateTime('publish_at')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');

            $table->foreignId('entry_type_id')
                ->index()
                ->constrained('entry_types')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entries');
    }
}
