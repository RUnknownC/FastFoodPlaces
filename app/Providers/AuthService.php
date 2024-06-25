<?php
// app/Providers/AuthServiceProvider.php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;
use App\Models\Comment; // Import the Comment model
use App\Policies\PostPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('update-post', [PostPolicy::class, 'update']);
        Gate::define('delete-post', [PostPolicy::class, 'delete']);

        // Define gate for deleting comments
        Gate::define('delete-comment', function ($user, Comment $comment) {
            // Admins can delete any comment
            if ($user->isAdmin()) {
                return true;
            }
            // Regular users can delete their own comments
            return $user->id === $comment->author_id;
        });
    }
}
