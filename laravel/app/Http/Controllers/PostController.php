<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $posts = Post::latest()->paginate(5);

        //render view with posts
        return view('posts.index', compact('posts'));
    }
    
    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //validate form
        $request->validate( [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }
    
    /**
     * edit
     *
     * @param  mixed $post
     * @return void
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, Post $post)
{
    // Validate form input
    $validatedData = $request->validate([
        'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'title'     => 'required|min:5',
        'content'   => 'required|min:10'
    ]);

    // Handle image update
    if ($request->hasFile('image')) {
        // Upload new image
        $image = $request->file('image');
        $imagePath = $image->store('public/posts');

        // Delete old image if it exists
        if ($post->image) {
            Storage::delete('public/posts/'.$post->image);
        }

        // Update post with new image
        $post->update([
            'image'     => $image->hashName(),
            'title'     => $validatedData['title'],
            'content'   => $validatedData['content']
        ]);
    } else {
        // Update post without changing the image
        $post->update([
            'title'     => $validatedData['title'],
            'content'   => $validatedData['content']
        ]);
    }

    // Redirect to index page
    return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
}

    
    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy(Post $post)
    {
        //delete image
        Storage::delete('public/posts/'. $post->image);

        //delete post
        $post->delete();

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}