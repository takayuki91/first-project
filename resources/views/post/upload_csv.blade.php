<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          CSVファイルをインポート
      </h2>
  </x-slot>

  <div class="max-w-7xl mx-auto px-6">
      <x-message :message="session('message')" />

      <form method="post" action="{{ route('post.process_upload') }}" enctype="multipart/form-data">
          @csrf
          <div class="mt-8">
              <div class="w-full flex flex-col">
                  <label for="csv_file" class="font-semibold mt-4">CSVファイルを選択</label>
                  <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
                  <input type="file" name="csv_file" class="w-auto py-2 border border-gray-300 rounded-md" id="csv_file">
              </div>
          </div>

          <x-primary-button class="mt-4">
              インポートする
          </x-primary-button>
      </form>
  </div>
</x-app-layout>