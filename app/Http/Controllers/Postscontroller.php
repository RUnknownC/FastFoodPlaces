<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate; // Import the Gate facade
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use App\Models\Comment;

class PostController extends Controller
{
    /**
     * Display a listing of the blog entries.
     */
    public function index()
    {
        // Reads all posts and all categories from the database
        $posts = Post::all()->sortByDesc('created_at');
        $categories = Category::all();
        return view('posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $users = User::all(); // Fetch all users
        return view('posts.create', compact('categories', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'author' => 'required|exists:users,id', // Validate author as user ID
            'body' => 'required',
            'category_id' => ['required', Rule::exists('categories', 'id')],
        ]);

        if ($validator->fails()) {
            return redirect()->route('posts.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $post = new Post();
        $post->title = $request->title;
        $post->author_id = $request->author; // Store author as user ID
        $post->body = $request->body;
        $post->category_id = $request->category_id;
        $post->save();

        return redirect()->route('posts.index')->with('success', 'Post created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);      
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the post.
     */
    public function edit($id)
{
    $post = Post::findOrFail($id);

    if (Gate::denies('update-post', $post)) {
        abort(403); // Unauthorized
    }

    $categories = Category::all();
    $users = User::all();

    return view('posts.edit', compact('post', 'categories', 'users'));
}
    /**
     * Update the post data in database.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);

        // Authorize the update-post gate
        Gate::authorize('update-post', $post);

        // Basic validation
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'author' => 'required|exists:users,id', // Validate author as user ID
            'body' => 'required',
            'category_id' => ['required', Rule::exists('categories', 'id')],
        ]);

        if ($validator->fails()) {
            return redirect()->route('posts.edit', $id)
                        ->withErrors($validator)
                        ->withInput();
        }

        // All clear - updating the post!
        $post->title = $request->title;
        $post->author_id = $request->author; // Store author as user ID
        $post->body = $request->body;
        $post->category_id = $request->category_id;
        $post->save();

        return redirect()->route('posts.index')->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);

        // Authorize the delete-post gate
        Gate::authorize('delete-post', $post);

        // Delete the post
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroyComment(string $postId, string $commentId)
    {
        $post = Post::findOrFail($postId);
        $comment = Comment::findOrFail($commentId);

        // Authorize the delete-comment gate
        Gate::authorize('delete-comment', $comment);

        // Delete the comment
        $comment->delete();

        return redirect()->route('posts.show', $postId)->with('success', 'Comment deleted successfully');
    }

}
