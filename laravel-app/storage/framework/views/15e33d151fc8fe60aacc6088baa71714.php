<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($task) ? 'Edit Task' : 'Create Task'); ?> - FlowSpec AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <?php echo $__env->make('layouts.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h2 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent flex items-center">
                <span class="material-icons text-4xl mr-3" style="color: #2563eb;"><?php echo e(isset($task) ? 'edit' : 'add_task'); ?></span>
                <?php echo e(isset($task) ? 'Edit Task' : 'Create New Task'); ?>

            </h2>
            <p class="text-gray-600 mt-2 text-lg"><?php echo e(isset($task) ? 'Update task details below' : 'Fill in the details to create a new task'); ?></p>
        </div>

        <?php if($errors->any()): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-xl mb-6 shadow-sm">
                <div class="flex items-start">
                    <span class="material-icons mr-3 mt-0.5">error</span>
                    <div class="flex-1">
                        <p class="font-semibold mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 p-8">
            <form action="<?php echo e(isset($task) ? route('tasks.update', $task) : route('tasks.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php if(isset($task)): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>

                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-medium mb-2">Title *</label>
                    <input type="text" name="title" id="title" value="<?php echo e(old('title', $task->title ?? '')); ?>" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo e(old('description', $task->description ?? '')); ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="status" class="block text-gray-700 font-medium mb-2">Status *</label>
                        <select name="status" id="status" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="pending" <?php echo e(old('status', $task->status ?? 'pending') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="in_progress" <?php echo e(old('status', $task->status ?? '') === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                            <option value="completed" <?php echo e(old('status', $task->status ?? '') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                            <option value="cancelled" <?php echo e(old('status', $task->status ?? '') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-gray-700 font-medium mb-2">Priority *</label>
                        <select name="priority" id="priority" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="low" <?php echo e(old('priority', $task->priority ?? 'medium') === 'low' ? 'selected' : ''); ?>>Low</option>
                            <option value="medium" <?php echo e(old('priority', $task->priority ?? 'medium') === 'medium' ? 'selected' : ''); ?>>Medium</option>
                            <option value="high" <?php echo e(old('priority', $task->priority ?? 'medium') === 'high' ? 'selected' : ''); ?>>High</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="due_date" class="block text-gray-700 font-medium mb-2">Due Date</label>
                        <input type="datetime-local" name="due_date" id="due_date" 
                               value="<?php echo e(old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '')); ?>"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="reminder_date" class="block text-gray-700 font-medium mb-2">Reminder Date</label>
                        <input type="datetime-local" name="reminder_date" id="reminder_date" 
                               value="<?php echo e(old('reminder_date', isset($task) && $task->reminder_date ? $task->reminder_date->format('Y-m-d\TH:i') : '')); ?>"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="project_id" class="block text-gray-700 font-medium mb-2">Project ID</label>
                    <input type="text" name="project_id" id="project_id" value="<?php echo e(old('project_id', $task->project_id ?? '')); ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="<?php echo e(route('tasks.index')); ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        <?php echo e(isset($task) ? 'Update Task' : 'Create Task'); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\MUSA\Documents\GitHub\Lomba2026\laravel-app\resources\views/tasks/create.blade.php ENDPATH**/ ?>