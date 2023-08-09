<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostDetailResource;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return PostDetailResource::collection($posts->loadMissing(['writer:id,email,username', 'comments:id,post_id,user_id,comments_content']));
    }

    public function showdetail($id)
    {
        $posts = Post::with('writer:id,email,username')->findOrFail($id);
        return new PostDetailResource($posts->loadMissing(['writer:id,email,username', 'comments:id,post_id,user_id,comments_content']));
    }

    public function showdetail2($id)
    {
        $posts = Post::findOrFail($id);
        return new PostDetailResource($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|max:255',
            'news_content'  => 'required',
        ]);

        $image = null;

        if ($request->file) {
            // Upload file
            $fileName   = $this->generateRandomString();
            $extension  = $request->file->extension();
            $image      = $fileName.'.'.$extension;

            Storage::putFileAs('image', $request->file, $image);
        }

        $request['image'] = $image;
        $request['author'] = Auth::user()->id;
        $post = Post::create($request->all());
        $postResource = new PostDetailResource($post->loadMissing('writer:id,email,username'));
        return response()->json($postResource, 200);
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'title'         => 'required|max:255',
            'news_content'  => 'required',
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->all());
        $postResource = new PostDetailResource($post->loadMissing('writer:id,email,username'));
        return response()->json($postResource, 200);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        $postResource = new PostDetailResource($post->loadMissing('writer:id,email,username'));
        return response()->json($postResource, 200);
    }

    function generateRandomString($length = 30) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
