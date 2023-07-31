<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          時短飯をダウンロード
      </h2>
  </x-slot>

  <div class="max-w-7xl mx-auto px-6 mt-4">
      <div class="text-center">
        <h1 class="text-lg font-semibold mb-4">
          全ての時短飯をダウンロードできます🍚<br>（形式:CSVファイル）
        </h1>
        <hr class="w-full mb-4">
          <a href="{{ route('post.download_csv_file') }}" class="download-btn">
              ダウンロード
          </a>
      </div>
  </div>
</x-app-layout>