<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(5);

        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function store(Request $request)
    {
        //validasi request
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        //check error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //simpan file gambar
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //simpan ke database
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        //balikan/return post resource
        return new PostResource(true, 'Data Berhasil Ditambahkan', $post);
    }
}
