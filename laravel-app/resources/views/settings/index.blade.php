<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - FlowSpec AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    @include('layouts.nav')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent flex items-center">
                <span class="material-icons text-4xl mr-3" style="color: #6366f1;">settings</span>
                Settings
            </h2>
            <p class="text-gray-600 mt-2 text-lg">Manage your account preferences and notifications</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-xl mb-6 shadow-sm flex items-center">
                <span class="material-icons mr-3">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 shadow-sm">
                <div class="flex items-start">
                    <span class="material-icons mr-3 mt-0.5">error</span>
                    <div class="flex-1">
                        <p class="font-semibold mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-6">
            <!-- Profile Information -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                    <span class="material-icons text-indigo-600 mr-3 text-2xl">person</span>
                    <h3 class="text-xl font-bold text-gray-800">Profile Information</h3>
                </div>
                
                <form action="{{ route('settings.update.profile') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="flex items-center text-gray-700 font-semibold mb-2">
                                <span class="material-icons text-sm mr-2 text-indigo-600">badge</span>
                                Name
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required
                                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition">
                        </div>

                        <div>
                            <label for="email" class="flex items-center text-gray-700 font-semibold mb-2">
                                <span class="material-icons text-sm mr-2 text-indigo-600">email</span>
                                Email
                            </label>
                            <input type="email" name="email" id="email" value="{{ auth()->user()->email }}" disabled
                                   class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-gray-50 text-gray-500 shadow-sm cursor-not-allowed">
                            <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition transform hover:scale-105 shadow-lg">
                                <span class="material-icons text-sm mr-2">save</span>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- WhatsApp Notifications -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                    <span class="material-icons text-green-600 mr-3 text-2xl">chat</span>
                    <h3 class="text-xl font-bold text-gray-800">WhatsApp Notifications</h3>
                </div>
                
                <form action="{{ route('settings.update.notifications') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <label for="phone_number" class="flex items-center text-gray-700 font-semibold mb-2">
                                <span class="material-icons text-sm mr-2 text-green-600">phone</span>
                                Phone Number
                            </label>
                            <input type="text" name="phone_number" id="phone_number" 
                                   value="{{ old('phone_number', auth()->user()->phone_number) }}"
                                   placeholder="e.g., 081234567890"
                                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent shadow-sm transition">
                            <p class="text-xs text-gray-500 mt-1">Enter your WhatsApp number (e.g., 081234567890)</p>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-start">
                                <input type="checkbox" name="whatsapp_notifications" id="whatsapp_notifications" 
                                       value="1" {{ auth()->user()->whatsapp_notifications ? 'checked' : '' }}
                                       class="mt-1 mr-3 h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <div class="flex-1">
                                    <label for="whatsapp_notifications" class="font-semibold text-gray-700 cursor-pointer flex items-center">
                                        <span class="material-icons text-sm mr-2 text-green-600">notifications_active</span>
                                        Enable WhatsApp Notifications
                                    </label>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Receive WhatsApp reminders for:
                                    </p>
                                    <ul class="text-sm text-gray-600 mt-2 space-y-1 ml-4">
                                        <li class="flex items-center">
                                            <span class="material-icons text-xs mr-2">check_circle</span>
                                            Tasks with deadline in 30 minutes
                                        </li>
                                        <li class="flex items-center">
                                            <span class="material-icons text-xs mr-2">check_circle</span>
                                            Meetings starting in 30 minutes
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        @if(auth()->user()->phone_number && auth()->user()->whatsapp_notifications)
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start">
                            <span class="material-icons text-blue-600 mr-3">info</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-blue-800">WhatsApp Notifications Active</p>
                                <p class="text-sm text-blue-700 mt-1">
                                    You will receive reminders at: <strong>{{ auth()->user()->phone_number }}</strong>
                                </p>
                            </div>
                        </div>
                        @endif

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold transition transform hover:scale-105 shadow-lg">
                                <span class="material-icons text-sm mr-2">save</span>
                                Save Notification Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Email Notifications -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                    <span class="material-icons text-blue-600 mr-3 text-2xl">email</span>
                    <h3 class="text-xl font-bold text-gray-800">Email Notifications</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start">
                        <span class="material-icons text-blue-600 mr-3">info</span>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-blue-800">Email Notifications Enabled</p>
                            <p class="text-sm text-blue-700 mt-1">
                                You receive email notifications at: <strong>{{ auth()->user()->email }}</strong>
                            </p>
                            <ul class="text-sm text-blue-700 mt-2 space-y-1 ml-4">
                                <li class="flex items-center">
                                    <span class="material-icons text-xs mr-2">check_circle</span>
                                    Task created and synced to Google Calendar
                                </li>
                                <li class="flex items-center">
                                    <span class="material-icons text-xs mr-2">check_circle</span>
                                    Meeting invitations with Google Meet link
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Google Account -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                    <span class="material-icons text-red-600 mr-3 text-2xl">account_circle</span>
                    <h3 class="text-xl font-bold text-gray-800">Google Account</h3>
                </div>
                
                <div class="space-y-4">
                    @if(auth()->user()->google_access_token)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="material-icons text-green-600 mr-3">check_circle</span>
                            <div>
                                <p class="font-semibold text-green-800">Google Account Connected</p>
                                <p class="text-sm text-green-700 mt-1">Calendar and Gmail integration active</p>
                            </div>
                        </div>
                        <form action="{{ route('settings.disconnect.google') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-semibold transition">
                                <span class="material-icons text-sm mr-1">logout</span>
                                Disconnect
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="material-icons text-yellow-600 mr-3">warning</span>
                            <div>
                                <p class="font-semibold text-yellow-800">Google Account Not Connected</p>
                                <p class="text-sm text-yellow-700 mt-1">Connect to enable Calendar and Gmail features</p>
                            </div>
                        </div>
                        <a href="{{ route('auth.google') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                            <span class="material-icons text-sm mr-1">link</span>
                            Connect
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
