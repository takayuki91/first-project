<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Postモデルを使うため
use App\Models\Post;


class PostController extends Controller
{
    public function create() {
        return view('post.create');
    }

    // 投稿データ保存
    public function store(Request $request) {
        // バリデーション
        $validator = $request->validate([
            'title' => 'required|max:50',
            'body' => 'required|max:100',
        ]);

        $post = Post::create($validated);
        return back();

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body
        ]);
        $request->session()->flash('message', '投稿に成功しました！');
        return back();
    }
}
