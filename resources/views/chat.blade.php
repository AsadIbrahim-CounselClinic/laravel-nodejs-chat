<x-app-layout>
    <div class="container mx-auto my-10">
        <div class="flex justify-center">
            <div class="w-full max-w-3xl">
                <div class="bg-white shadow-lg rounded-lg">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-center py-4 rounded-t-lg">
                        <h4 class="text-lg font-semibold">Chat Room</h4>
                        <p class="text-sm opacity-80">Connect and communicate instantly</p>
                    </div>

                    <!-- Messages -->
                    <div 
                        class="p-4 space-y-4 overflow-y-auto" 
                        style="height: 450px; background-color: #f7f9fc;" 
                        id="messages">
                        @forelse ($messages as $message)
                            <div class="flex items-start space-x-4">
                                <!-- Avatar -->
                                <div class="w-10 h-10 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                                    {{ substr($message->user->name, 0, 1) }}
                                </div>
                                <!-- Message Content -->
                                <div>
                                    <p class="font-semibold text-indigo-500">{{ $message->user->name }}</p>
                                    <p class="text-gray-800">{{ $message->message }}</p>
                                    <p class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500">No messages yet. Start the conversation!</p>
                        @endforelse
                    </div>

                    <!-- Footer -->
                    <div class="p-4 bg-gray-100 rounded-b-lg">
                        <form id="messageForm" class="flex items-center">
                            <div class="flex w-full shadow">
                                <!-- Text Input -->
                                <textarea 
                                    id="message" 
                                    name="message" 
                                    class="w-full px-4 py-2 rounded-l-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                    placeholder="Type your message here..."
                                    rows="1" 
                                    style="resize: none;"
                                ></textarea>
                                <!-- Send Button -->
                                <button 
                                    type="submit" 
                                    class="flex items-center px-4 py-2 bg-indigo-500 text-white text-sm font-medium rounded-r-lg hover:bg-indigo-600 focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 transition duration-150"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M15.854 1.854a.5.5 0 0 1 0 .707l-14 14a.5.5 0 1 1-.708-.708L14.293 2.5H9.5a.5.5 0 0 1 0-1h6a.5.5 0 0 1 .354.146z"/>
                                        <path d="M13.5 2.5v-1h-1v1h1z"/>
                                    </svg>
                                    Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.socket.io/4.5.1/socket.io.min.js"></script>
    <script>
        const socket = io('http://localhost:3000');

        document.getElementById('messageForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const message = document.getElementById('message').value;

            const response = await fetch('/messages', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({ message })
            });

            if (response.ok) {
                const data = await response.json();
                socket.emit('new-message', data);
                document.getElementById('message').value = '';
            }
        });

        socket.on('receive-message', function (message) {
            const messagesDiv = document.getElementById('messages');
            const newMessage = `
                <div class="flex items-start space-x-4 mb-4">
                    <div class="w-10 h-10 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                        ${message.name.charAt(0)}
                    </div>
                    <div>
                        <p class="font-semibold text-indigo-500">${message.name}</p>
                        <p class="text-gray-800">${message.message}</p>
                        <p class="text-sm text-gray-500">Just now</p>
                    </div>
                </div>
            `;
            messagesDiv.innerHTML += newMessage;
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        });
    </script>
</x-app-layout>