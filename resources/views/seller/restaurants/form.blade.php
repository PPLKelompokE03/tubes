<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $restaurant ? __('Ubah restoran') : __('Tambah restoran') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="post"
                      action="{{ $restaurant ? route('restaurants.update', $restaurant) : route('restaurants.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-6">
                    @csrf
                    @if ($restaurant)
                        @method('put')
                    @endif

                    <div>
                        <x-input-label for="name" :value="__('Nama restoran')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $restaurant?->name)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('Alamat')" />
                        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $restaurant?->address)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $restaurant?->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div>
                        <x-input-label for="image" :value="__('Gambar (opsional, JPG/PNG, maks. 2MB)')" />
                        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/40 dark:file:text-indigo-200" />
                        <x-input-error class="mt-2" :messages="$errors->get('image')" />
                        @if ($restaurant?->image)
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Gambar saat ini tersimpan. Unggah file baru untuk mengganti.') }}</p>
                        @endif
                    </div>

                    <div>
                        <x-input-label for="menu_access_pin" :value="$restaurant ? __('PIN kelola menu (opsional, isi jika ingin mengganti)') : __('PIN kelola menu (4-8 digit)')" />
                        <x-text-input id="menu_access_pin" name="menu_access_pin" type="password" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full" :required="! $restaurant" />
                        <x-input-error class="mt-2" :messages="$errors->get('menu_access_pin')" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('PIN ini dipakai saat masuk ke halaman kelola menu restoran.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <x-primary-button>{{ $restaurant ? __('Simpan perubahan') : __('Simpan restoran') }}</x-primary-button>
                        <a href="{{ route('seller.restaurants') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">{{ __('Batal') }}</a>
                    </div>
                </form>

                @if ($restaurant)
                    <form method="post" action="{{ route('restaurants.destroy', $restaurant) }}" class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700"
                          onsubmit="return confirm({{ json_encode(__('Hapus restoran ini? Menu terkait juga akan terhapus.'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }});">
                        @csrf
                        @method('delete')
                        <x-danger-button type="submit">{{ __('Hapus restoran') }}</x-danger-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
