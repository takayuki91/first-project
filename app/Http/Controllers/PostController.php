<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Postモデルを使うため
use App\Models\Post;
// Gateを使うため
use Illuminate\Support\Facades\Gate;
// csvファイルをアップロードするため
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use League\Csv\Reader;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    // 投稿データ作成
    public function create() {
        return view('post.create');
    }

    // 投稿データ保存
    public function store(Request $request) {
        // Gate追加
        Gate::authorize('admin');

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

    // // 投稿一覧
    // public function index() {
    //     // Gate追加
    //     Gate::authorize('admin');

    //     $posts=Post::all();
    //     return view('post.index', compact('posts'));
    // }

    // 一覧投稿（検索機能）
    public function index(Request $request) {
        $keyword = $request->input('keyword');
        $query = Post::query();
        if(!empty($keyword)) {
            $query->where('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('body', 'LIKE', "%{$keyword}%");
        }
        $posts = $query->orderBy('created_at', 'desc')
                       ->paginate(5);
        return view('post.index', compact('posts', 'keyword'));
    }

    // 投稿詳細
    public function show (post $post) {
        // Gate追加
        Gate::authorize('admin');

        return view('post.show', compact('post'));
    }

    // 投稿編集
    public function edit(Post $post) {
         // Gate追加
         Gate::authorize('admin');

        return view('post.edit', compact('post'));
    }

    // 投稿更新
    public function update(Request $request, Post $post) {
        // Gate追加
        Gate::authorize('admin');

        // バリデーション
        $validated = $request->validate([
            'title' => 'required|max:50',
            'body' => 'required|max:100',
        ]);

        $validated['user_id'] = auth()->id();

        $post->update($validated);

        $request->session()->flash('message', '投稿を更新しました！');
        return back();
    }

    // 投稿削除
    public function destroy(Request $request, Post $post) {
        // Gate追加
        Gate::authorize('admin');

        $post->delete();
        $request->session()->flash('message', '削除に成功しました');
        return redirect()->route('post.index');
    }

    // CSVファイルをアップロード
    public function showUploadForm()
    {
        return view('post/upload_csv');
    }

    public function processUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->path();

        // ファイルを読み込む処理
        $fileContent = File::get($filePath);

        // 例：CSVファイルを行ごとに分割し、それぞれの行を処理する場合
        $rows = explode("\n", $fileContent);
        // 1行目はヘッダーなのでスキップする
        // $header = array_shift($rows);
        unset($rows[0]);
        
        foreach ($rows as $row) {
            $data = str_getcsv($row);
            // $data にはCSVファイルの各列が配列として格納される
            // 例：$data[0] が1列目のデータ、$data[1] が2列目のデータなど

            // user_id を設定
            // $data['user_id'] = auth()->id();
            // ここでデータベースに保存するなどの処理を行う
            // Post::insert(['title' => $row[0], 'body' => $row[1], 'user_id' => auth()->id()]);
            if (isset($data[0]) && isset($data[1])) {
                $data['user_id'] = auth()->id();
                Post::create(['title' => $data[0], 'body' => $data[1], 'user_id' => $data['user_id']]);
            }
        }

        return redirect()->route('post.index')->with('message', 'CSVファイルのインポートが完了しました。');
    }
}
