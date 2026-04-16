<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pilih Restoran') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">{{ __('← Kembali ke dashboard') }}</a>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($restaurants as $restaurant)
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $restaurant->name }}</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $restaurant->address }}</p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $restaurant->description ?: '-' }}</p>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Jumlah menu tersedia') }}: {{ $restaurant->mystery_boxes_count }}
                        </p>

                        <div class="mt-4">
                            <a href="{{ route('dashboard.restaurants.show', $restaurant) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                                {{ __('Lihat menu restoran') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 text-gray-500 dark:text-gray-400">
                        {{ __('Belum ada restoran yang tersedia.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
