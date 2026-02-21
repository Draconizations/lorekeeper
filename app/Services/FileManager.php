<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | File Manager
    |--------------------------------------------------------------------------
    |
    | Handles uploading and manipulation of files.
    |
    */

    /**
     * Creates a directory.
     *
     * @param string $dir
     *
     * @return bool
     */
    public function createDirectory($dir) {
        $disk = Storage::disk(getDisk($dir));
        if ($disk->directoryExists($dir)) {
            $this->setError('error', 'Folder already exists.');
        } else {
            // Create the directory.
            if (!$disk->makeDirectory($dir)) {
                $this->setError('error', 'Failed to create folder.');

                return false;
            }
        }

        return true;
    }

    /**
     * Deletes a directory if it exists and doesn't contain files.
     *
     * @param string $dir
     *
     * @return bool
     */
    public function deleteDirectory($dir) {
        $disk = Storage::disk(getDisk($dir));

        if (!$disk->directoryExists($dir)) {
            $this->setError('error', 'Directory does not exist.');

            return false;
        }
        if (count($disk->allFiles($dir))) {
            $this->setError('error', 'Cannot delete a folder that contains files.');

            return false;
        }
        $disk->deleteDirectory($dir);

        return true;
    }

    /**
     * Renames a directory.
     *
     * @param string $dir
     * @param string $oldName
     * @param string $newName
     *
     * @return bool
     */
    public function renameDirectory($dir, $oldName, $newName) {
        $disk = Storage::disk(getDisk($dir));

        if (!$disk->exists($dir.'/'.$oldName)) {
            $this->setError('error', 'Directory does not exist.');

            return false;
        }
        if (count($disk->allFiles($dir))) {
            $this->setError('error', 'Cannot delete a folder that contains files.');

            return false;
        }
        $disk->move($dir.'/'.$oldName, $dir.'/'.$newName);

        return true;
    }

    /**
     * Uploads a file.
     *
     * @param array  $file
     * @param string $dir
     * @param string $name
     * @param bool   $isFileManager
     *
     * @return bool
     */
    public function uploadFile($file, $dir, $name, $isFileManager = true) {
        $directory = ($isFileManager ? '/files'.($dir ? '/'.$dir : '') : '/images');

        $disk = Storage::disk(getDisk($directory));
        if (!$disk->directoryExists($directory)) {
            $this->setError('error', 'Folder does not exist.');

            return false;
        }
        if (!$disk->putFileAs($directory, $file, $name)) {
            $this->setError('error', 'Could not upload file.');

            return false;
        }

        return true;
    }

    /**
     * Uploads a custom CSS file.
     *
     * @param array $file
     *
     * @return bool
     */
    public function uploadCss($file) {
        $disk = Storage::disk(getDisk('/css/custom'));
        $disk->move($file, '/css/custom.css');

        return true;
    }

    /**
     * Deletes a file.
     *
     * @param string $file
     *
     * @return bool
     */
    public function deleteFile($file) {
        $disk = Storage::disk(getDisk($file));
        if (!$disk->exists($file)) {
            $this->setError('error', 'File does not exist.');

            return false;
        }
        $disk->delete($file);

        return true;
    }

    /**
     * Moves a file.
     *
     * @param string $oldDir
     * @param string $newDir
     * @param string $name
     *
     * @return bool
     */
    public function moveFile($oldDir, $newDir, $name) {
        if (!Storage::disk(getDisk($oldDir))->fileExists($oldDir.'/'.$name)) {
            $this->setError('error', 'File does not exist.');

            return false;
        } elseif (!Storage::disk(getDisk($newDir)->directoryExists($newDir)) {
            $this->setError('error', 'Destination does not exist.');

            return false;
        }
        $file = Storage::disk(getDisk($oldDir))->get($oldDir.'/'.$name);

        if (!Storage::disk(getDisk($oldDir))->delete($oldDir.'/'.$name)) {
            $this->setError('error', 'Failed to move file.');

            return false;
        }

        if (!Storage::disk(getDisk($oldDir))->putFileAs($newDir, $file, $name)) {
            $this->setError('error', 'Failed to move file.');

            return false;
        }

        return true;
    }

    /**
     * Renames a file.
     *
     * @param string $dir
     * @param string $oldName
     * @param string $newName
     *
     * @return bool
     */
    public function renameFile($dir, $oldName, $newName) {
        $disk = Storage::disk(getDisk($dir));

        if (!$disk->fileExists($dir.'/'.$oldName)) {
            $this->setError('error', 'File does not exist.');

            return false;
        }
        $disk->move($dir.'/'.$oldName, $dir.'/'.$newName);

        return true;
    }
}
