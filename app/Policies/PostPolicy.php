<?php

// app/Policies/PostPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    /**
     * Determine if the given post can be updated by the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return bool
     */
    public function update(User $user, Post $post)
    {
        return $user->id === $post->author_id;
    }

    /**
     * Determine if the given post can be deleted by the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return bool
     */
    public function delete(User $user, Post $post)
    {
        // Admins can delete any post
        if ($user->isAdmin()) {
            return true;
        }

        // Non-admins can only delete their own posts
        return $user->id === $post->author_id;
    }
}

