<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $title ?? 'Restaurant List' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="py-2">Name</th>
                            <th class="py-2">Address</th>
                            <th class="py-2">Description</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-900 dark:text-gray-100">
                        @forelse($restaurants as $restaurant)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="py-2">{{ $restaurant->name }}</td>
                                <td class="py-2">{{ $restaurant->address }}</td>
                                <td class="py-2">{{ $restaurant->description ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-3 text-gray-500 dark:text-gray-400">No restaurants found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
