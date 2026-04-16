<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kelola Menu') }} - {{ $restaurant->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 px-1">
                <a href="{{ route('seller.restaurants') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">{{ __('← Kembali ke restoran') }}</a>
                <a href="{{ route('seller.mystery-boxes.create', ['restaurant_id' => $restaurant->id]) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">{{ __('+ Tambah menu') }}</a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="py-2">{{ __('Nama') }}</th>
                            <th class="py-2">{{ __('Harga') }}</th>
                            <th class="py-2">{{ __('Diskon') }}</th>
                            <th class="py-2">{{ __('Harga Final') }}</th>
                            <th class="py-2">{{ __('Stok') }}</th>
                            <th class="py-2 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-900 dark:text-gray-100">
                        @forelse ($mysteryBoxes as $box)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="py-2">{{ $box->name }}</td>
                                <td class="py-2">Rp {{ number_format((float) $box->price, 0, ',', '.') }}</td>
                                <td class="py-2">{{ (int) $box->discount_percentage }}%</td>
                                <td class="py-2">Rp {{ number_format((float) $box->final_price, 0, ',', '.') }}</td>
                                <td class="py-2">{{ $box->stock }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('seller.mystery-boxes.edit', $box) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">{{ __('Ubah') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-3 text-gray-500 dark:text-gray-400">{{ __('Belum ada menu pada restoran ini.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
