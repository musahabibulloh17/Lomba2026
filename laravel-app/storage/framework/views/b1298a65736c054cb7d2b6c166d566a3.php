<nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center transform group-hover:scale-105 transition duration-200">
                        <span class="material-icons text-white text-xl">auto_awesome</span>
                    </div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent hidden sm:block">
                        FlowSpec AI
                    </h1>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex items-center space-x-1">
                <a href="<?php echo e(route('dashboard')); ?>" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition duration-200
                          <?php echo e(request()->routeIs('dashboard') 
                             ? 'bg-indigo-50 text-indigo-700' 
                             : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600'); ?>">
                    <span class="material-icons text-lg mr-1.5">dashboard</span>
                    <span class="hidden md:inline">Dashboard</span>
                </a>
                
                <a href="<?php echo e(route('tasks.index')); ?>" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition duration-200
                          <?php echo e(request()->routeIs('tasks.*') 
                             ? 'bg-indigo-50 text-indigo-700' 
                             : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600'); ?>">
                    <span class="material-icons text-lg mr-1.5">task_alt</span>
                    <span class="hidden md:inline">Tasks</span>
                </a>
                
                <a href="<?php echo e(route('meetings.index')); ?>" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition duration-200
                          <?php echo e(request()->routeIs('meetings.*') 
                             ? 'bg-indigo-50 text-indigo-700' 
                             : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600'); ?>">
                    <span class="material-icons text-lg mr-1.5">event</span>
                    <span class="hidden md:inline">Meetings</span>
                </a>
                
                <a href="<?php echo e(route('chat')); ?>" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition duration-200
                          <?php echo e(request()->routeIs('chat') 
                             ? 'bg-indigo-50 text-indigo-700' 
                             : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600'); ?>">
                    <span class="material-icons text-lg mr-1.5">chat</span>
                    <span class="hidden md:inline">Chat</span>
                </a>
                
                <a href="<?php echo e(route('settings')); ?>" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition duration-200
                          <?php echo e(request()->routeIs('settings*') 
                             ? 'bg-indigo-50 text-indigo-700' 
                             : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600'); ?>">
                    <span class="material-icons text-lg mr-1.5">settings</span>
                    <span class="hidden md:inline">Settings</span>
                </a>

                <!-- User Menu -->
                <div class="flex items-center ml-4 pl-4 border-l border-gray-200">
                    <div class="flex items-center bg-gray-50 rounded-xl px-3 py-2 mr-2">
                        <span class="material-icons text-gray-600 text-lg mr-2">account_circle</span>
                        <span class="text-sm font-medium text-gray-700 hidden sm:inline"><?php echo e(auth()->user()->name); ?></span>
                    </div>
                    
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" 
                                class="flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl text-sm font-medium transition duration-200 transform hover:scale-105 shadow-sm">
                            <span class="material-icons text-lg mr-1.5">logout</span>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', sans-serif;
    }
</style>
<?php /**PATH C:\Users\MUSA\Documents\GitHub\Lomba2026\laravel-app\resources\views/layouts/nav.blade.php ENDPATH**/ ?>