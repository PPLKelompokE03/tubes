@php
    $lockCreate = $restaurants->isEmpty() && ! $mysteryBox;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $mysteryBox ? __('Ubah menu (mystery box)') : __('Tambah menu (mystery box)') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if ($restaurants->isEmpty())
                <div class="mb-6 rounded-md bg-amber-50 text-amber-900 dark:bg-amber-900/20 dark:text-amber-100 px-4 py-3 text-sm">
                    {{ __('Anda belum punya restoran. Buat restoran dulu, lalu kembali ke halaman ini.') }}
                    <a href="{{ route('seller.restaurants.create') }}" class="font-medium underline ms-1">{{ __('Tambah restoran') }}</a>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="post"
                      action="{{ $mysteryBox ? route('mystery-boxes.update', $mysteryBox) : route('mystery-boxes.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-6">
                    @csrf
                    @if ($mysteryBox)
                        @method('put')
                    @endif

                    @if (! $mysteryBox)
                        <div>
                            <x-input-label for="restaurant_id" :value="__('Restoran')" />
                            <select id="restaurant_id" name="restaurant_id" required
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    @if ($lockCreate) disabled @endif>
                                <option value="">{{ __('— Pilih restoran —') }}</option>
                                @foreach ($restaurants as $r)
                                    <option value="{{ $r->id }}" @selected((string) old('restaurant_id', $selectedRestaurantId ?? '') === (string) $r->id)>{{ $r->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('restaurant_id')" />
                        </div>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Restoran') }}: <span class="font-medium text-gray-900 dark:text-gray-100">{{ $mysteryBox->restaurant?->name }}</span>
                        </p>
                    @endif

                    <div>
                        <x-input-label for="name" :value="__('Nama menu')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $mysteryBox?->name)" :disabled="$lockCreate" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                  @if ($lockCreate) disabled @endif>{{ old('description', $mysteryBox?->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="price" :value="__('Harga (Rp)')" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $mysteryBox?->price)" :disabled="$lockCreate" required />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>
                        <div>
                            <x-input-label for="discount_percentage" :value="__('Diskon (%)')" />
                            <x-text-input id="discount_percentage" name="discount_percentage" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('discount_percentage', $mysteryBox?->discount_percentage ?? 0)" :disabled="$lockCreate" />
                            <x-input-error class="mt-2" :messages="$errors->get('discount_percentage')" />
                        </div>
                    </div>

                    @if ($mysteryBox)
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Harga akhir dihitung otomatis') }}: Rp {{ number_format((float) $mysteryBox->final_price, 0, ',', '.') }}
                        </p>
                    @endif

                    <div>
                        <x-input-label for="stock" :value="__('Stok')" />
                        <x-text-input id="stock" name="stock" type="number" min="1" step="1" class="mt-1 block w-full" :value="old('stock', $mysteryBox?->stock)" :disabled="$lockCreate" required />
                        <x-input-error class="mt-2" :messages="$errors->get('stock')" />
                    </div>

                    <div>
                        <x-input-label for="image" :value="__('Gambar (opsional, JPG/PNG, maks. 2MB)')" />
                        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/40 dark:file:text-indigo-200"
                               @if ($lockCreate) disabled @endif />
                        <x-input-error class="mt-2" :messages="$errors->get('image')" />
                        @if ($mysteryBox?->image)
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Gambar saat ini tersimpan. Unggah file baru untuk mengganti.') }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <x-primary-button type="submit" :disabled="$lockCreate">
                            {{ $mysteryBox ? __('Simpan perubahan') : __('Simpan menu') }}
                        </x-primary-button>
                        <a href="{{ route('seller.mystery-boxes') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">{{ __('Batal') }}</a>
                    </div>
                </form>

                @if ($mysteryBox)
                    <form method="post" action="{{ route('mystery-boxes.destroy', $mysteryBox) }}" class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700"
                          onsubmit="return confirm({{ json_encode(__('Hapus menu ini?'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }});">
                        @csrf
                        @method('delete')
                        <x-danger-button type="submit">{{ __('Hapus menu') }}</x-danger-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
