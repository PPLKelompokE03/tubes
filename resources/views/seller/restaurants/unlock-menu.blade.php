<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Masuk Kelola Menu') }} - {{ $restaurant->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('Masukkan PIN sederhana restoran untuk membuka akses kelola menu.') }}
                </p>

                <form method="POST" action="{{ route('seller.restaurants.unlock-menu.post', $restaurant) }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="menu_access_pin" :value="__('PIN Restoran')" />
                        <x-text-input id="menu_access_pin" name="menu_access_pin" type="password" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('menu_access_pin')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>{{ __('Masuk') }}</x-primary-button>
                        <a href="{{ route('seller.restaurants') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">{{ __('Kembali') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
