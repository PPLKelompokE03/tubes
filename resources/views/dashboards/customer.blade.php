<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Customer Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalOrders ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Paid/Completed Orders</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $paidOrders ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Spent</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format((float) ($totalSpent ?? 0), 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Order History</h3>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('dashboard.restaurants') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">
                            Browse Restaurants
                        </a>
                        <a href="{{ route('dashboard.orders') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                            View All
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="py-2">Mystery Box</th>
                                <th class="py-2">Quantity</th>
                                <th class="py-2">Total Price</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-900 dark:text-gray-100">
                            @forelse(($orders ?? collect()) as $order)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="py-2">{{ $order->mysteryBox?->name ?? '-' }}</td>
                                    <td class="py-2">{{ $order->quantity }}</td>
                                    <td class="py-2">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                    <td class="py-2 capitalize">{{ $order->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 text-gray-500 dark:text-gray-400">No orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
