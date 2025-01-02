<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Display success message -->
            @if (session('success'))
                <div class="bg-green-500 text-white p-4 rounded-md mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @foreach($users as $user)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold text-indigo-500">{{ $user->name }}</p>
                            <p class="text-light-800">{{ $user->email }}</p>
                        </div>
                        <div class="ml-auto">
                            <form action="{{ route('create-chat-room', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-opacity-75">
                                    Create Chat Room With {{ $user->name }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach    
        </div>
    </div>
</x-app-layout>
