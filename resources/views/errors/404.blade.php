<!DOCTYPE html>
<html lang="en" class="h-full bg-zinc-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-zinc-900 border border-zinc-800 rounded-2xl p-8 text-center shadow-2xl">
        <div class="text-7xl font-bold text-indigo-500 mb-6 tracking-tight">404</div>
        <h1 class="text-2xl font-bold text-white mb-2">Page Not Found</h1>
        <p class="text-zinc-400 mb-8 leading-relaxed">
            The page you're looking for doesn't exist or has been moved.
        </p>
        <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-indigo-500 transition-colors">
            Go Back
        </button>
    </div>
</body>
</html>
