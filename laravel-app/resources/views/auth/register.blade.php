<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FlowSpec AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
        }
        .input-with-icon {
            padding-left: 40px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <!-- Logo and Header -->
        <div class="text-center mb-8 animate-fade-in">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-lg mb-4">
                <span class="material-icons text-white text-3xl">rocket_launch</span>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Create Account
            </h1>
            <p class="text-gray-600 mt-2 font-medium">Join FlowSpec AI Platform</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border border-gray-100 p-8 animate-fade-in" style="animation-delay: 0.1s;">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-xl mb-6 flex items-start">
                    <span class="material-icons text-green-500 mr-3 mt-0.5">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
                @csrf
                
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <span class="material-icons text-sm mr-1">person</span>
                            Full Name
                        </span>
                    </label>
                    <div class="relative">
                        <span class="material-icons input-icon">badge</span>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="w-full input-with-icon px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200 bg-gray-50 hover:bg-white"
                            placeholder="John Doe"
                            required
                            value="{{ old('name') }}"
                        >
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-2 flex items-center">
                            <span class="material-icons text-xs mr-1">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <span class="material-icons text-sm mr-1">email</span>
                            Email Address
                        </span>
                    </label>
                    <div class="relative">
                        <span class="material-icons input-icon">alternate_email</span>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="w-full input-with-icon px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200 bg-gray-50 hover:bg-white"
                            placeholder="user@example.com"
                            required
                            value="{{ old('email') }}"
                        >
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-2 flex items-center">
                            <span class="material-icons text-xs mr-1">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <span class="material-icons text-sm mr-1">lock</span>
                            Password
                        </span>
                    </label>
                    <div class="relative">
                        <span class="material-icons input-icon">key</span>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="w-full input-with-icon px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200 bg-gray-50 hover:bg-white"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-2 flex items-center">
                            <span class="material-icons text-xs mr-1">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center">
                            <span class="material-icons text-sm mr-1">verified_user</span>
                            Confirm Password
                        </span>
                    </label>
                    <div class="relative">
                        <span class="material-icons input-icon">lock_reset</span>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            class="w-full input-with-icon px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200 bg-gray-50 hover:bg-white"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-3.5 px-6 rounded-xl transition duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center mt-6"
                >
                    <span class="material-icons mr-2">person_add</span>
                    Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500 font-medium">Or continue with</span>
                </div>
            </div>

            <!-- Google Sign Up Button -->
            <div class="mb-6">
                <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 font-semibold bg-white hover:bg-gray-50 hover:border-gray-300 transition duration-200 shadow-sm hover:shadow group">
                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    <span class="group-hover:text-gray-900 transition">Sign up with Google</span>
                </a>
            </div>

            <!-- Sign In Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold inline-flex items-center hover:underline">
                        Sign in here
                        <span class="material-icons text-sm ml-1">arrow_forward</span>
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center animate-fade-in" style="animation-delay: 0.2s;">
            <p class="text-gray-500 text-xs flex items-center justify-center">
                <span class="material-icons text-xs mr-1">code</span>
                Powered by Laravel 12 | PHP 8.4 | PostgreSQL
            </p>
        </div>
    </div>
</body>
</html>
