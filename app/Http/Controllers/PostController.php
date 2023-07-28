<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Postモデルを使うため
use App\Models\Post;


class PostController extends Controller
{
    // 投稿データ作成
    public function create() {
        return view('post.create');
    }

    // 投稿データ保存
    public function store(Request $request) {
        // バリデーション
        $validated = $request->validate([
            'title' => 'required|max:50',
            'body' => 'required|max:100',
        ]);

        $validated['user_id'] = auth()->id();

        $post = Post::create($validated);

        $request->session()->flash('message', '投稿に成功しました！');
        return back();
    }

    // 投稿一覧
    public function index() {
        $posts=Post::all();
        return view('post.index', compact('posts'));
    }
}
