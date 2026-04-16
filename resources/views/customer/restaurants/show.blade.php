<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Menu Restoran') }} - {{ $restaurant->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <a href="{{ route('dashboard.restaurants') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">{{ __('← Kembali ke daftar restoran') }}</a>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($mysteryBoxes as $box)
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $box->name }}</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $box->description ?: '-' }}</p>
                        <div class="mt-3 text-sm text-gray-500 dark:text-gray-400 space-y-1">
                            <p>{{ __('Harga normal') }}: Rp {{ number_format((float) $box->price, 0, ',', '.') }}</p>
                            <p>{{ __('Diskon') }}: {{ (int) $box->discount_percentage }}%</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Harga final') }}: Rp {{ number_format((float) $box->final_price, 0, ',', '.') }}</p>
                            <p>{{ __('Stok') }}: {{ $box->stock }}</p>
                        </div>

                        <form method="POST" action="{{ route('orders.store') }}" class="mt-4 flex items-end gap-3">
                            @csrf
                            <input type="hidden" name="mystery_box_id" value="{{ $box->id }}">
                            <div>
                                <x-input-label for="quantity_{{ $box->id }}" :value="__('Jumlah')" />
                                <x-text-input id="quantity_{{ $box->id }}" name="quantity" type="number" min="1" :max="$box->stock" value="1" class="mt-1 w-24" required />
                            </div>
                            <x-primary-button>{{ __('Pesan') }}</x-primary-button>
                        </form>

                        @if ($errors->has('mystery_box_id') || $errors->has('quantity'))
                            <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                        @endif
                    </div>
                @empty
                    <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 text-gray-500 dark:text-gray-400">
                        {{ __('Menu pada restoran ini belum tersedia atau stok habis.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
