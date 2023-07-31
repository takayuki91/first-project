<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          一覧表示
      </h2>
  </x-slot>

  <div class="mx-auto px-6">
  {{-- コンポーネントに変更 --}}
  <x-message :message="session('message')" />
  </div>

  {{-- 検索機能 --}}
  <div class="mx-auto mt-2 p-4 bg-white w-full" style="text-align: center;">
    <h1 class="text-lg font-semibold">
      もっと時短したい人へ⌛️
    </h1>
    <hr class="w-full">
    <form action="{{ route('post.index') }}" method="GET">
      <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="キーワード入力" aria-label="キーワード検索">
      <x-primary-button class="mt-4">
        <input type="submit" value="検索">
      </x-primary-button>
    </form>
  </div>
  {{-- 検索機能 --}}

  <div class="mx-auto px-6">
    {{-- @if(session('message'))
      <div class="text-black-600 font-bold">
        {{session('message')}}
      </div>
    @endif --}}

    @foreach($posts as $post)
    <div class="mt-4 p-8 bg-white w-full rounded-2xl">
      <h1 class="mt-4 text-lg font-semibold">
        <a href="{{route('post.show', $post)}}" class="text-green-600">
          {{$post->title}}
        </a>
      </h1>
      <hr class="w-full">
      <p class="mt-4 p-4">
        {{$post->body}}
      </p>
      <div class="p-4 text-sm font-semibold flex flex-row-reverse">
        <p>
          {{$post->created_at}} by {{$post->user->name??'Anonymous'}}
        </p>
      </div>
    </div>
    @endforeach
    <div class="mb-4">
      {{ $posts->links() }}
    </div>
  </div>
</x-app-layout>
