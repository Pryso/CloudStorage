<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->char('id',20)->primary();
            $table->string("path");
            $table->string("url");
            $table->string("extension");
            $table->string("name");
            $table->enum('access',['PUBLIC','PRIVATE'])->default('PRIVATE');
            $table->bigInteger('size');
            $table->string("full_name");
            $table->foreignId("user_id")->references('id')->on('users');
            $table->foreignId("folder_id")->nullable()->references('id')->on('folders');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
