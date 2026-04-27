<?php

use Illuminate\Support\Facades\Route;

// Serve React SPA for all non-API routes
Route::get('/{any?}', function () {
    $indexFile = public_path('index.html');
    if (file_exists($indexFile)) {
        return file_get_contents($indexFile);
    }
    return response()->json(['message' => 'Frontend not built yet. Run npm run build in castelo-group-front.'], 404);
})->where('any', '^(?!api|storage).*$');
