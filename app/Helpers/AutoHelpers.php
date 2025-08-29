<?php

/**
 * Generates the asset URL
 */
function asset($path, $secure = null) {
    $remote = false;
    foreach (config('lorekeeper.storage.remote_assets') as $asset_path) {
        if (str_starts_with($path, $asset_path)) {
            $remote = true;
        }
    }
    if ($remote) {
        return Illuminate\Support\Facades\Storage::url($path);
    } else {
        return app('url')->asset($path, $secure);
    }
}