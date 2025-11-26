<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller {
    /**
     * Shows the files index.
     *
     * @param string $folder
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex($folder = null) {
        $filesDirectory = '/files';

        // Create the files directory if it doesn't already exist.
        if (!Storage::directoryExists($filesDirectory)) {
            // Create the directory.
            if (!Storage::makeDirectory($filesDirectory)) {
                $this->abort(500);

                return false;
            }
        }
        if ($folder && !Storage::directoryExists($filesDirectory.'/'.$folder)) {
            abort(404);
        }
        $dir = $filesDirectory.($folder ? '/'.$folder : '');
        $files = Storage::files($dir);

        return view('admin.files.index', [
            'folder'  => $folder,
            'folders' => Storage::directories($dir),
            'files'   => $files,
        ]);
    }

    /**
     * Creates a new directory in the files directory.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateFolder(Request $request, FileManager $service) {
        $request->validate(['name' => 'required|alpha_dash']);

        if ($service->createDirectory('/files/'.$request->get('name'))) {
            flash('Folder created successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Moves a file in the files directory.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postMoveFile(Request $request, FileManager $service) {
        $request->validate(['destination' => 'required']);
        $oldDir = $request->get('folder');
        $newDir = $request->get('destination');

        if ($service->moveFile(
            '/files'.($oldDir ? '/'.$oldDir : ''),
            '/files'.($newDir != 'root' ? '/'.$newDir : ''),
            $request->get('filename')
        )) {
            flash('File moved successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Renames a file in the files directory.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRenameFile(Request $request, FileManager $service) {
        $request->validate(['name' => 'required|regex:/^[a-z0-9\._-]+$/i']);
        $dir = $request->get('folder');
        $oldName = $request->get('filename');
        $newName = $request->get('name');

        if ($service->renameFile('/files'.($dir ? '/'.$dir : ''), $oldName, $newName)) {
            flash('File renamed successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Deletes a file in the files directory.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFile(Request $request, FileManager $service) {
        $request->validate(['filename' => 'required']);
        $dir = $request->get('folder');
        $name = $request->get('filename');

        if ($service->deleteFile('/files'.($dir ? '/'.$dir : '').'/'.$name)) {
            flash('File deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Uploads a file to the files directory.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUploadFile(Request $request, FileManager $service) {
        $request->validate(['files.*' => 'file|required']);
        $dir = $request->get('folder');
        $files = $request->file('files');
        foreach ($files as $file) {
            if ($service->uploadFile($file, $dir, $file->getClientOriginalName())) {
                flash('File uploaded successfully.')->success();
            } else {
                foreach ($service->errors()->getMessages()['error'] as $error) {
                    flash($error)->error();
                }
            }
        }

        return redirect()->back();
    }

    /**
     * Renames a directory in the files directory.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRenameFolder(Request $request, FileManager $service) {
        $request->validate(['name' => 'required|regex:/^[a-z0-9\._-]+$/i']);
        $dir = '/files';
        $oldName = $request->get('folder');
        $newName = $request->get('name');

        if ($service->renameDirectory($dir, $oldName, $newName)) {
            flash('Folder renamed successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->back();
        }

        return redirect()->to('admin/files/'.$newName);
    }

    /**
     * Deletes a directory in the files directory.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFolder(Request $request, FileManager $service) {
        $request->validate(['folder' => 'required']);
        $dir = $request->get('folder');

        if ($service->deleteDirectory('/files/'.$dir)) {
            flash('Folder deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->back();
        }

        return redirect()->to('admin/files');
    }

    /**
     * Shows the site images index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSiteImages() {
        return view('admin.files.images', [
            'images' => config('lorekeeper.image_files'),
        ]);
    }

    /**
     * Uploads a site image file.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUploadImage(Request $request, FileManager $service) {
        $request->validate(['file' => 'required|file']);
        $file = $request->file('file');
        $key = $request->get('key');
        $filename = config('lorekeeper.image_files.'.$key)['filename'];

        if ($service->uploadFile($file, null, $filename, false)) {
            flash('Image uploaded successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Uploads a custom site CSS file.
     *
     * @param App\Services\FileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUploadCss(Request $request, FileManager $service) {
        $request->validate(['file' => 'required|file']);
        $file = $request->file('file');

        if ($service->uploadCss($file)) {
            flash('File uploaded successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
