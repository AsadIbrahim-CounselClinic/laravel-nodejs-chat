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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 rounded-lg shadow-lg mb-8">
                <h2 class="text-2xl font-bold">Your Chat Rooms</h2>
                <p class="text-sm opacity-90 mt-1">Connect and collaborate with your team</p>
            </div>
    
            <!-- Chat Rooms List -->
            <div class="space-y-6">
                @foreach($chatRooms as $chatRoom)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-start space-x-6">
                                <!-- Participants Avatars -->
                                <div class="flex -space-x-4">
                                    @foreach($chatRoom->participants as $participant)
                                        <div class="w-12 h-12 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-semibold ring-2 ring-white dark:ring-gray-800">
                                            {{ substr($participant->user->name, 0, 1) }}
                                        </div>
                                    @endforeach
                                </div>
    
                                <!-- Chat Room Details -->
                                <div class="flex-1">
                                    {{-- <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $chatRoom->name }}</h3> --}}
                                    <div class="mt-2 space-y-1">
                                        @foreach($chatRoom->participants as $participant)
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                <span class="font-medium text-indigo-500">{{ $participant->user->name }}</span>
                                                <span class="text-gray-400">• {{ $participant->user->email }}</span>
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
    
                                <!-- Join Button -->
                                <div class="flex items-center">
                                    <a 
                                        href="{{ route('join-chat-room', $chatRoom->name) }}" 
                                        target="_blank" 
                                        class="bg-indigo-500 text-white py-2 px-6 rounded-lg hover:bg-indigo-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2"
                                    >
                                        Join Room
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
</x-app-layout>
