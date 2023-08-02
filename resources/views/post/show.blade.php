<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          投稿詳細
      </h2>
  </x-slot>

  <div class="max-w-7xl mx-auto px-6">
    <div class="bg-white w-full rounded-2xl">
      <div class="mt-4 p-4">
        <h1 class="text-lg font-semibold">
          {{ $post->title }}
        </h1>

        @can('admin')
          <div class="mb-2 text-right flex">
            <a href="{{route('post.edit', $post)}}" class="flex-1">
              <x-primary-button>
                編集する
              </x-primary-button>
            </a>

            <form method="post" action="{{route('post.destroy', $post)}}" class="flex-2">
              @csrf
              @method('delete')
              <x-primary-button class="bg-red-800 ml-2">
                削除する
              </x-primary-button>
            </form>
          </div>
        @endcan

        <hr class="w-full">
        <p class="mt-4 whitespace-pre-line">
          {{ $post->body }}
        </p>
        @if($post->image)
          <img src="{{ asset('storage/images/'.$post->image)}}" class="mx-auto" style="height:300px;">
          <div>
            (画像ファイル：{{$post->image}})
          </div>
        @endif
        <div class="text-sm font-semibold flex flex-row-reverse">
          <p> {{ $post->created_at }} by {{$post->user->name??'Anonymous'}}</p>
        </div>
      </div>
    </div>
  </div>

</x-app-layout>