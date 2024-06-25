<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConvertingToUserReferences extends Migration
{
    public function up()
    {
        // Add author_id column to posts table and set up foreign key
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
        });

        // Add author_id column to comments table and set up foreign key
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
        });

        // Drop author column from posts table if it exists
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'author')) {
                $table->dropColumn('author');
            }
        });

        // Drop author column from comments table if it exists
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'author')) {
                $table->dropColumn('author');
            }
        });
    }

    public function down()
    {
        // Remove foreign key and author_id column from posts table by recreating the table
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('author_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('author_id');
        });

        // Re-add author column to posts table if needed
        Schema::table('posts', function (Blueprint $table) {
            $table->string('author')->nullable();
        });

        // Re-add author column to comments table if needed
        Schema::table('comments', function (Blueprint $table) {
            $table->string('author')->nullable();
        });
    }
}
