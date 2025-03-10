<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Exception;

class PostController extends Controller
{
    public function index(Request $request)
    {
       try {
            $query = Post::query();
            $perPage = 1;
            $page = $request->input('page', 1);
            $search = $request->input('search');

            if ($search) {
                $query->whereRaw("title LIKE '%" . $search . "%'");
            }

            $total = $query->count();

            $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

            return response()->json([
                'status' => 200,
                'status_message' => 'Post recovered',
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'items' => $result
            ]);
       } catch (Exception $e) {
            return response()->json($e);
       }
    }
    public function store(CreatePostRequest $request)
    {
        try {
            $post = new Post();
            $post->title = $request->title;
            $post->description = $request->description;
            $post->user_id = auth()->user()->id;

            $post->save();

            return response()->json([
                'status' => 201,
                'status_message' => 'Post added',
                'data' => $post
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function show(Post $post)
    {
        return response()->json($post);
    }

    public function update(UpdatePostRequest $request,Post $post)
    {
        try{
            $post->title = $request->title;
            $post->description = $request->description;
            $post->user_id = auth()->user()->id;

            if ($post->user_id === auth()->user()->id) {
                $post->update();
            }

            return response()->json([
                'status' => 200,
                'status_message' => 'Post Updated',
                'data' => $post
            ]);
        }catch (Exception $e) {
            return response()->json($e);
        }   
    }
    public function destroy(Post $post)
    {
        try {
            if ($post) {
                if ($post->user_id === auth()->user()->id) {
                    $post->delete();
                }

                return response()->json([
                    'status' => 200,
                    'status_message' => 'Post Deleted',
                    'data' => $post
                ]);
            } else  {
                return response()->json([
                    'status' => 422,
                    'status_message' => 'Post not found',
                    'data' => $post
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
