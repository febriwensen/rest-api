<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id'           => 'required|exists:posts,id',
            'comments_content'  => 'required',
        ]);

        $request['user_id'] = Auth::user()->id;

        $comment = Comment::create($request->all());

        $commentResource = new CommentResource($comment->loadMissing('komentator:id,email,username'));
        return response()->json($commentResource, 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'comments_content'  => 'required',
        ]);

        $comment = Comment::findOrFail($id);
        $comment->update($request->all());
        $commentResource = new CommentResource($comment->loadMissing('komentator:id,email,username'));
        return response()->json($commentResource, 200);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        $commentResource = new CommentResource($comment->loadMissing('komentator:id,email,username'));
        return response()->json($commentResource, 200);
    }
}
