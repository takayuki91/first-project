<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          CSVファイルをダウンロード
      </h2>
  </x-slot>

  <div class="max-w-7xl mx-auto px-6">
    <a href="{{ route('post.download') }}">CSVファイルをダウンロード</a>
  </div>
</x-app-layout>