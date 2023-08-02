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

// csvファイルをダウンロードするため
use League\Csv\Writer;
use Illuminate\Support\Facades\DB;


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
            'body' => 'required|max:140',
            'image'=>'image|max:1024'
        ]);

        $validated['user_id'] = auth()->id();

        // 画像についての処理
        if (request('image')){
            $name = request()->file('image')->getClientOriginalName();
            // 日時追加
            $name = date('Ymd_His').'_'.$name;
            request()->file('image')->move('storage/images', $name);
            $validated['image'] = $name;
        }
        
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
            'body' => 'required|max:140',
            'image'=>'image|max:1024'
        ]);

        $validated['user_id'] = auth()->id();

        if(request('image')){
            $name=request()->file('image')->getClientOriginalName();
            $name=date('Ymd_His').'_'.$name;
            request()->file('image')->move('storage/images', $name);
            $validated['image'] = $name;
        
            // 古い画像ファイルが存在する場合は削除する
            if ($post->image) {
                $oldImagePath = public_path('storage/images/' . $post->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        } else {
            // 新しい画像がアップロードされなかった場合
            unset($validated['image']);
        
            $post->image=$name;
        }

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

        // CSVファイルを行ごとに分割し、それぞれの行を処理
        $rows = explode("\n", $fileContent);
        // 1行目はヘッダーなのでスキップ
        unset($rows[0]);
        
        foreach ($rows as $row) {
            $data = str_getcsv($row);
            // $dataにCSVファイルの各列が配列として格納

            // データベースに保存する処理を行う
            if (isset($data[0]) && isset($data[1])) {
                $data['user_id'] = auth()->id();
                Post::create(['title' => $data[0], 'body' => $data[1], 'user_id' => $data['user_id']]);
            }
        }

        return redirect()->route('post.index')->with('message', 'CSVファイルのインポートが完了しました。');
    }

    // csvファイルをダウンロード
    public function showDownloadForm()
    {
        return view('post/download_csv');
    }

    public function downloadCSV() {
        $fileName = 'example.csv';
        $csvData = $this->getCSVData();
    
        $csv = Writer::createFromPath('php://temp', 'w');
        $csv->insertAll($csvData);
    
        return response($csv->getContent(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
    
    public function getCSVData() {
    $posts = Post::select('title', 'body')->get();
    $csvData = [];
    foreach ($posts as $post) {
        $csvData[] = [$post->title, $post->body];
    }
    return $csvData;
    }
}
