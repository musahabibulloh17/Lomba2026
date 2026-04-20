<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat Assistant - FlowSpec AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .message-animation {
            animation: slideIn 0.3s ease-out;
        }
        .chat-container {
            height: calc(100vh - 300px);
            min-height: 500px;
        }
        /* Custom scrollbar */
        .chat-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .chat-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .chat-scrollbar::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 10px;
        }
        .chat-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a5b4fc;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    @include('layouts.nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="chatApp()" x-init="init()">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent flex items-center">
                        <span class="material-icons text-4xl mr-3" style="color: #4F46E5;">smart_toy</span>
                        AI Chat Assistant
                    </h2>
                    <p class="text-gray-600 mt-2 text-lg">Ask me anything about your tasks, meetings, or workflow!</p>
                </div>
                <div class="flex items-center space-x-2 bg-green-50 px-4 py-2 rounded-xl border border-green-200">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-green-700">AI Online</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chat Area -->
            <div class="lg:col-span-2">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100">
                    <!-- Chat Messages -->
                    <div class="chat-container overflow-y-auto p-6 space-y-4 chat-scrollbar" id="chatMessages" x-ref="chatMessages">
                        @if($recentCommands->isEmpty())
                            <div class="text-center text-gray-500 py-12">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full mb-4">
                                    <span class="material-icons text-indigo-600 text-5xl">chat_bubble_outline</span>
                                </div>
                                <p class="text-lg font-semibold text-gray-700">No messages yet. Start a conversation!</p>
                                <p class="text-sm mt-2 text-gray-500">Try asking: "Create a task for tomorrow" or "Show my upcoming meetings"</p>
                            </div>
                        @else
                            @foreach($recentCommands as $command)
                                <!-- User Message -->
                                <div class="flex justify-end message-animation">
                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl rounded-tr-sm px-5 py-3 max-w-md shadow-lg">
                                        <p class="text-sm leading-relaxed">{{ $command->command }}</p>
                                        <div class="flex items-center justify-end mt-1 space-x-1">
                                            <span class="material-icons text-xs">schedule</span>
                                            <span class="text-xs text-indigo-100">{{ $command->created_at->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- AI Response -->
                                <div class="flex justify-start message-animation">
                                    <div class="bg-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-5 py-3 max-w-md shadow-md border border-gray-200">
                                        <div class="flex items-center mb-2">
                                            <span class="material-icons text-indigo-600 text-sm mr-1">smart_toy</span>
                                            <span class="text-xs font-semibold text-indigo-600">FlowSpec AI</span>
                                        </div>
                                        <p class="text-sm leading-relaxed whitespace-pre-line">{{ $command->response }}</p>
                                        <div class="flex items-center mt-1 space-x-1">
                                            <span class="material-icons text-xs text-gray-400">schedule</span>
                                            <span class="text-xs text-gray-500">{{ $command->created_at->addSeconds(1)->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <!-- New Messages (Alpine.js) -->
                        <template x-for="(message, index) in messages" :key="message.id">
                            <div>
                                <!-- User Message -->
                                <div class="flex justify-end mb-4 message-animation">
                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl rounded-tr-sm px-5 py-3 max-w-md shadow-lg">
                                        <p class="text-sm leading-relaxed" x-text="message.text"></p>
                                        <div class="flex items-center justify-end mt-1 space-x-1">
                                            <span class="material-icons text-xs">schedule</span>
                                            <span class="text-xs text-indigo-100" x-text="message.time"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- AI Response -->
                                <div class="flex justify-start mb-4 message-animation" x-show="message.response && message.response.length > 0">
                                    <div class="bg-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-5 py-3 max-w-md shadow-md border border-gray-200">
                                        <div class="flex items-center mb-2">
                                            <span class="material-icons text-indigo-600 text-sm mr-1">smart_toy</span>
                                            <span class="text-xs font-semibold text-indigo-600">FlowSpec AI</span>
                                        </div>
                                        <p class="text-sm leading-relaxed whitespace-pre-line" x-text="message.response"></p>
                                        <div class="flex items-center mt-1 space-x-1">
                                            <span class="material-icons text-xs text-gray-400">schedule</span>
                                            <span class="text-xs text-gray-500" x-text="message.responseTime"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Loading -->
                        <div x-show="loading" class="flex justify-start message-animation">
                            <div class="bg-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-5 py-3 shadow-md border border-gray-200">
                                <div class="flex items-center mb-2">
                                    <span class="material-icons text-indigo-600 text-sm mr-1">smart_toy</span>
                                    <span class="text-xs font-semibold text-indigo-600">FlowSpec AI is typing...</span>
                                </div>
                                <div class="flex space-x-2">
                                    <div class="w-2.5 h-2.5 bg-indigo-400 rounded-full animate-bounce"></div>
                                    <div class="w-2.5 h-2.5 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                    <div class="w-2.5 h-2.5 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="border-t border-gray-200 p-4 bg-gray-50/50 rounded-b-2xl">
                        <form @submit.prevent="sendMessage" class="flex space-x-3">
                            <div class="flex-1 relative">
                                <span class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">edit</span>
                                <input 
                                    type="text" 
                                    x-model="messageText"
                                    placeholder="Type your message here..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white shadow-sm transition"
                                    :disabled="loading"
                                    @keydown.enter="sendMessage"
                                >
                            </div>
                            <button 
                                type="submit"
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg flex items-center space-x-2"
                                :disabled="loading || !messageText.trim()"
                            >
                                <span x-show="!loading">Send</span>
                                <span x-show="loading">Sending...</span>
                                <span class="material-icons" x-show="!loading">send</span>
                                <span class="material-icons animate-spin" x-show="loading">refresh</span>
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-2 flex items-center">
                            <span class="material-icons text-xs mr-1">info</span>
                            Press Enter to send your message
                        </p>
                    </div>
                </div>
            </div>

            <!-- Suggestions Panel -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Commands -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 p-6">
                    <div class="flex items-center mb-4">
                        <span class="material-icons text-indigo-600 mr-2">lightbulb</span>
                        <h3 class="text-lg font-bold text-gray-800">Quick Commands</h3>
                    </div>
                    <div class="space-y-2">
                        <button @click="setMessage('Create a task for tomorrow')" 
                                class="w-full text-left px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 rounded-xl text-sm transition border border-blue-100 flex items-center group">
                            <span class="material-icons text-blue-600 text-sm mr-2">add_task</span>
                            <span class="flex-1">Create a task for tomorrow</span>
                            <span class="material-icons text-gray-400 text-sm opacity-0 group-hover:opacity-100 transition">arrow_forward</span>
                        </button>
                        <button @click="setMessage('Show my upcoming meetings')" 
                                class="w-full text-left px-4 py-3 bg-gradient-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 rounded-xl text-sm transition border border-green-100 flex items-center group">
                            <span class="material-icons text-green-600 text-sm mr-2">event</span>
                            <span class="flex-1">Show my upcoming meetings</span>
                            <span class="material-icons text-gray-400 text-sm opacity-0 group-hover:opacity-100 transition">arrow_forward</span>
                        </button>
                        <button @click="setMessage('List pending tasks')" 
                                class="w-full text-left px-4 py-3 bg-gradient-to-r from-yellow-50 to-orange-50 hover:from-yellow-100 hover:to-orange-100 rounded-xl text-sm transition border border-yellow-100 flex items-center group">
                            <span class="material-icons text-yellow-600 text-sm mr-2">pending_actions</span>
                            <span class="flex-1">List pending tasks</span>
                            <span class="material-icons text-gray-400 text-sm opacity-0 group-hover:opacity-100 transition">arrow_forward</span>
                        </button>
                        <button @click="setMessage('Schedule a meeting for next week')" 
                                class="w-full text-left px-4 py-3 bg-gradient-to-r from-purple-50 to-pink-50 hover:from-purple-100 hover:to-pink-100 rounded-xl text-sm transition border border-purple-100 flex items-center group">
                            <span class="material-icons text-purple-600 text-sm mr-2">calendar_month</span>
                            <span class="flex-1">Schedule a meeting for next week</span>
                            <span class="material-icons text-gray-400 text-sm opacity-0 group-hover:opacity-100 transition">arrow_forward</span>
                        </button>
                        <button @click="setMessage('What are my high priority tasks?')" 
                                class="w-full text-left px-4 py-3 bg-gradient-to-r from-red-50 to-rose-50 hover:from-red-100 hover:to-rose-100 rounded-xl text-sm transition border border-red-100 flex items-center group">
                            <span class="material-icons text-red-600 text-sm mr-2">priority_high</span>
                            <span class="flex-1">What are my high priority tasks?</span>
                            <span class="material-icons text-gray-400 text-sm opacity-0 group-hover:opacity-100 transition">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- AI Status -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 p-6">
                    <div class="flex items-center mb-4">
                        <span class="material-icons text-indigo-600 mr-2">settings</span>
                        <h4 class="text-lg font-bold text-gray-800">AI Status</h4>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-xl border border-green-100">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-green-700">Gemini AI Active</p>
                                <p class="text-xs text-green-600">Ready to assist you</p>
                            </div>
                        </div>
                        
                        <!-- Clear History Button -->
                        <button @click="clearHistory" 
                                class="w-full p-3 bg-red-50 hover:bg-red-100 rounded-xl border border-red-200 transition flex items-center justify-center space-x-2 group">
                            <span class="material-icons text-red-600 text-sm">delete_sweep</span>
                            <span class="text-sm font-semibold text-red-700">Clear Chat History</span>
                        </button>
                        
                        <div class="p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                            <div class="flex items-center mb-1">
                                <span class="material-icons text-indigo-600 text-sm mr-1">auto_awesome</span>
                                <p class="text-xs font-semibold text-indigo-700">Powered by</p>
                            </div>
                            <p class="text-sm font-bold text-indigo-800">Google Gemini 2.5</p>
                        </div>
                        
                        <!-- Debug Button -->
                        <button 
                            @click="testAPI()" 
                            class="w-full px-4 py-3 bg-gradient-to-r from-yellow-100 to-orange-100 hover:from-yellow-200 hover:to-orange-200 text-yellow-800 rounded-xl text-sm font-semibold border border-yellow-200 flex items-center justify-center space-x-2 transition transform hover:scale-105">
                            <span class="material-icons text-sm">bug_report</span>
                            <span>Test API Connection</span>
                        </button>
                    </div>
                </div>

                <!-- Message Count -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90 mb-1">Messages Today</p>
                            <p class="text-3xl font-bold" x-text="messages.length + {{ $recentCommands->count() }}">{{ $recentCommands->count() }}</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-xl">
                            <span class="material-icons text-3xl">chat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function chatApp() {
            return {
                messageText: '',
                messages: [],
                loading: false,
                
                init() {
                    console.log('Chat app initialized');
                    // Scroll to bottom on init
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                },
                
                setMessage(text) {
                    this.messageText = text;
                    // Optionally auto-send after setting message
                    // this.$nextTick(() => this.sendMessage());
                },
                
                scrollToBottom() {
                    const chatContainer = this.$refs.chatMessages;
                    if (chatContainer) {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                },
                
                async sendMessage() {
                    if (!this.messageText.trim()) {
                        console.log('Empty message, skipping');
                        return;
                    }
                    
                    console.log('=== SENDING MESSAGE ===');
                    console.log('Message:', this.messageText);
                    
                    const message = {
                        id: Date.now(),
                        text: this.messageText,
                        time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
                        response: '',
                        responseTime: ''
                    };
                    
                    // Add message to array IMMEDIATELY for instant UI update
                    this.messages.push(message);
                    const userMessage = this.messageText;
                    this.messageText = '';
                    this.loading = true;
                    
                    console.log('Message added to array, total messages:', this.messages.length);
                    
                    // Scroll to bottom after adding user message
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                    
                    try {
                        console.log('Sending request to server...');
                        const response = await fetch('{{ route("chat.send") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: userMessage
                            })
                        });
                        
                        console.log('Response status:', response.status);
                        
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Error response:', errorText);
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        console.log('✅ Response data received:', data);
                        
                        // Update the message object DIRECTLY
                        if (data.success && data.response) {
                            // Find the message in the array and update it
                            const messageIndex = this.messages.findIndex(m => m.id === message.id);
                            if (messageIndex !== -1) {
                                this.messages[messageIndex].response = data.response;
                                this.messages[messageIndex].responseTime = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                                console.log('✅ Message updated at index:', messageIndex);
                                console.log('✅ Response set to:', this.messages[messageIndex].response);
                            }
                        } else {
                            message.response = data.error || 'Failed to get response from AI.';
                            message.responseTime = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                        }
                        
                        // Force Alpine to detect changes
                        this.$nextTick(() => {
                            console.log('✅ After nextTick, messages array:', this.messages);
                            this.scrollToBottom();
                        });
                        
                    } catch (error) {
                        console.error('❌ Error:', error);
                        message.response = '❌ Sorry, there was an error processing your request. Error: ' + error.message;
                        message.responseTime = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    } finally {
                        this.loading = false;
                        
                        // Final scroll to bottom
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    }
                },
                
                async testAPI() {
                    console.log('🔧 Testing API...');
                    
                    const testMessage = {
                        id: Date.now(),
                        text: '🔧 Testing API Connection...',
                        time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
                        response: '',
                        responseTime: ''
                    };
                    
                    this.messages.push(testMessage);
                    this.loading = true;
                    
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                    
                    try {
                        const response = await fetch('/test-chat-api', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: 'test connection'
                            })
                        });
                        
                        console.log('🔧 Test API Status:', response.status);
                        const data = await response.json();
                        console.log('🔧 Test API Response:', data);
                        
                        testMessage.response = '✅ API Test Successful!\n\nResponse: ' + (data.response || JSON.stringify(data));
                        testMessage.responseTime = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                        
                    } catch (error) {
                        console.error('🔧 Test API Error:', error);
                        testMessage.response = '❌ API Test Failed!\n\nError: ' + error.message;
                        testMessage.responseTime = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    }
                },
                
                async clearHistory() {
                    if (!confirm('Are you sure you want to clear all chat history? This action cannot be undone.')) {
                        return;
                    }
                    
                    console.log('🗑️ Clearing chat history...');
                    
                    try {
                        const response = await fetch('{{ route("chat.clear") }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        
                        console.log('Clear history response status:', response.status);
                        
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Error response:', errorText);
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        console.log('✅ Clear history response:', data);
                        
                        if (data.success) {
                            // Clear messages array
                            this.messages = [];
                            
                            // Reload page to refresh the history
                            window.location.reload();
                        } else {
                            alert('Failed to clear history: ' + (data.message || 'Unknown error'));
                        }
                        
                    } catch (error) {
                        console.error('❌ Error clearing history:', error);
                        alert('Failed to clear history: ' + error.message);
                    }
                }
            }
        }
    </script>
</body>
</html>
