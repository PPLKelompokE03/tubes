<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $title ?? 'Order List' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="py-2">Order ID</th>
                            <th class="py-2">Customer</th>
                            <th class="py-2">Mystery Box</th>
                            <th class="py-2">Quantity</th>
                            <th class="py-2">Total Price</th>
                            <th class="py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-900 dark:text-gray-100">
                        @forelse($orders as $order)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="py-2">#{{ $order->id }}</td>
                                <td class="py-2">{{ $order->user?->name ?? '-' }}</td>
                                <td class="py-2">{{ $order->mysteryBox?->name ?? '-' }}</td>
                                <td class="py-2">{{ $order->quantity }}</td>
                                <td class="py-2">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                <td class="py-2 capitalize">{{ $order->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-3 text-gray-500 dark:text-gray-400">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
