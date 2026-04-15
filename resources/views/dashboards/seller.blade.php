<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Seller Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Owned Restaurants</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $restaurants->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Mystery Boxes</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $mysteryBoxes->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Incoming Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $incomingOrders->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format((float) $totalSales, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('seller.restaurants') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">View Restaurants</a>
                    <a href="{{ route('seller.mystery-boxes') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">View Mystery Boxes</a>
                    <a href="{{ route('seller.orders') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">View Incoming Orders</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
