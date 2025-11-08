<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_post_tag', function (Blueprint $table) {
            $table->uuid('blog_post_id');
            $table->uuid('tag_id');
            $table->timestamps();

            // Primary key
            $table->primary(['blog_post_id', 'tag_id']);

            // Foreign key constraints
            $table->foreign('blog_post_id')
                ->references('id')
                ->on('blog_posts')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade');

            // Indexes
            $table->index('blog_post_id');
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_post_tag');
    }
};
