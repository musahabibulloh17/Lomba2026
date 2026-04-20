<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Details - <?php echo e($meeting->title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <?php echo $__env->make('components.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800"><?php echo e($meeting->title); ?></h1>
                    <p class="text-gray-600 mt-1">Meeting Details</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?php echo e(route('meetings.edit', $meeting->id)); ?>" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Edit Meeting
                    </a>
                    <a href="<?php echo e(route('meetings.index')); ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Meeting Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Status Badge -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 text-sm rounded-full font-medium
                                <?php if($meeting->status === 'scheduled'): ?> bg-blue-100 text-blue-800
                                <?php elseif($meeting->status === 'completed'): ?> bg-green-100 text-green-800
                                <?php elseif($meeting->status === 'cancelled'): ?> bg-red-100 text-red-800
                                <?php else: ?> bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php echo e(ucfirst($meeting->status)); ?>

                            </span>
                            <span class="px-3 py-1 text-sm rounded-full font-medium bg-purple-100 text-purple-800">
                                <?php echo e(ucfirst(str_replace('_', ' ', $meeting->meeting_type))); ?>

                            </span>
                        </div>
                        <span class="text-sm text-gray-500">
                            Created <?php echo e($meeting->created_at->diffForHumans()); ?>

                        </span>
                    </div>
                </div>

                <!-- Meeting Details -->
                <div class="px-6 py-6 space-y-6">
                    <!-- Time Information -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Time & Schedule</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Start Time</p>
                                    <p class="text-sm font-medium text-gray-800"><?php echo e(\Carbon\Carbon::parse($meeting->start_time)->format('l, M j, Y')); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo e(\Carbon\Carbon::parse($meeting->start_time)->format('g:i A')); ?></p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">End Time</p>
                                    <p class="text-sm font-medium text-gray-800"><?php echo e(\Carbon\Carbon::parse($meeting->end_time)->format('l, M j, Y')); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo e(\Carbon\Carbon::parse($meeting->end_time)->format('g:i A')); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center space-x-2 text-sm text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Duration: <?php echo e(\Carbon\Carbon::parse($meeting->start_time)->diffInMinutes(\Carbon\Carbon::parse($meeting->end_time))); ?> minutes</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <?php if($meeting->description): ?>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Description</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 whitespace-pre-line"><?php echo e($meeting->description); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Location -->
                    <?php if($meeting->location): ?>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Location</h3>
                        <div class="flex items-center space-x-2 text-gray-700">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span><?php echo e($meeting->location); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Meeting Link -->
                    <?php if($meeting->meeting_link): ?>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Meeting Link</h3>
                        <div class="flex items-center space-x-2">
                            <a href="<?php echo e($meeting->meeting_link); ?>" target="_blank" class="flex items-center space-x-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Join Meeting</span>
                            </a>
                            <button onclick="copyToClipboard('<?php echo e($meeting->meeting_link); ?>')" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Reminder -->
                    <?php if($meeting->reminder_minutes): ?>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Reminder</h3>
                        <div class="flex items-center space-x-2 text-gray-700">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span><?php echo e($meeting->reminder_minutes); ?> minutes before</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Footer Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        Last updated <?php echo e($meeting->updated_at->diffForHumans()); ?>

                    </div>
                    <div class="flex space-x-2">
                        <?php if($meeting->status === 'scheduled'): ?>
                        <form action="<?php echo e(route('meetings.update', $meeting->id)); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition">
                                Mark as Completed
                            </button>
                        </form>
                        <form action="<?php echo e(route('meetings.update', $meeting->id)); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="px-4 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition">
                                Cancel Meeting
                            </button>
                        </form>
                        <?php endif; ?>
                        <form action="<?php echo e(route('meetings.destroy', $meeting->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this meeting?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Meeting link copied to clipboard!');
            });
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\MUSA\Documents\GitHub\Lomba2026\laravel-app\resources\views/meetings/show.blade.php ENDPATH**/ ?>