<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalUsers ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Restaurants</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalRestaurants ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalOrders ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format((float) ($totalSales ?? 0), 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">System Activity (Recent Orders)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="py-2">Customer</th>
                                <th class="py-2">Restaurant</th>
                                <th class="py-2">Mystery Box</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-900 dark:text-gray-100">
                            @forelse(($recentOrders ?? collect()) as $order)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="py-2">{{ $order->user?->name ?? '-' }}</td>
                                    <td class="py-2">{{ $order->mysteryBox?->restaurant?->name ?? '-' }}</td>
                                    <td class="py-2">{{ $order->mysteryBox?->name ?? '-' }}</td>
                                    <td class="py-2 capitalize">{{ $order->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 text-gray-500 dark:text-gray-400">No recent system activity.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
