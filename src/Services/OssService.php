<?php

declare(strict_types = 1);

namespace Framework\Baseapp\Services;

use Illuminate\Support\Facades\Storage;
use Swoolecan\Foundation\Services\TraitOssService;

class OssService extends AbstractService
{
    use TraitOssService;

    protected $storage;

    public function setStorage($driver)
    {
        $this->storage = Storage::disk($driver);
        return $this->storage;
    }

    public function getStorage()
    {
        return empty($this->storage) ? $this->setStorage('ossfree') : $this->storage;
    }

    public function dealPut($file, $sourceFile, $type = 'local')
    {
        //$sourceFile = 'http://upfile.canliang.wang/bench/base/book/cover/90/90f2ca28693e3c10c272dea1fe447332.jpg';
        //$sourceFile = '/data/htmlwww/filesys/bench/base/book/cover/fb/fb1dfa56d3d36f8db2886dbfeea2706c.jpg';
        $storage = $this->getStorage();
        switch ($type) {
        case 'local':
            return $storage->putFile($file, $sourceFile); // upload file from local path
        case 'remote':
            return $storage->putRemoteFile($file, $sourceFile); //upload remote file to storage by remote url
        case 'content':
            return $storage->put($file, $sourceFile); // first parameter is the target file path, second paramter is file content
        }
    }

    public function getUrl($file)
    {
        //$file = 'book/30e2f4b5-6ba0-4e12-87e1-f8af668140b2.jpg';
        return $this->getStorage()->url($file); // get the file url
    }

    public function dealFile($oldFile, $newFile)
    {
        Storage::copy($oldFile, $newFile);
        Storage::move($oldFile, $newFile);
        Storage::rename($oldFile, $newFile);
    }

    public function fileData($file)
    {
        $file = 'book/30e2f4b5-6ba0-4e12-87e1-f8af668140b2.jpg';
        //return $this->getStorage()->get($file); // get the file object by path
        //return $this->getStorage()->exists($file); // determine if a given file exists on the storage(OSS)
        //return $this->getStorage()->size($file); // get the file size (Byte)
        return $this->getStorage()->lastModified($file); // get date of last modification
    }

    public function dealDirectory($directory = '')
    {
        return $this->getStorage()->allDirectories($directory);
        Storage::directories($directory); // Get all of the directories within a given directory
        Storage::allDirectories($directory); // Get all (recursive) of the directories within a given directory
        Storage::makeDirectory($directory); // Create a directory.
        Storage::deleteDirectory($directory); // Recursively delete a directory.It will delete all files within a given directory, SO Use with caution please.
    }

    public function dealFileLists($directory = '')
    {
        //fetch all files of specified bucket(see upond configuration)
        return $this->getStorage()->allFiles($directory);
        return $this->getStorage()->files($directory);
    }

    public function commmon()
    {
        Storage::disk('oss'); // if default filesystems driver is oss, you can skip this step
        
        Storage::prepend('file.log', 'Prepended Text'); // Prepend to a file.
        Storage::append('file.log', 'Appended Text'); // Append to a file.
        
        Storage::delete('file.jpg');
        Storage::delete(['file1.jpg', 'file2.jpg']);
    }

    public function dealOldAttachment()
    {
        $results = \DB::connection()->table(\DB::raw('workbench_passport.wp_attachment'))->get();
        $attachmentModel = $this->resource->getObject('model', 'passport-attachment');
        $attachmentInfoModel = $this->resource->getObject('model', 'passport-attachmentInfo');
        $bookModel = $this->resource->getObject('model', 'culture-book');
        foreach ($results as $result) {
            if ($result->info_table != 'book') {
                continue;
            }
            $book = $bookModel->where('id', $result->info_id)->first();
            if (empty($book)) {
                print_r($result);
                continue;
            }
            $file = 'book/cover/' . $book->code . '.' . $result->extname;
            $aData = [
                'system' => 'ossfree',
                'path_id' => 3,
                'filepath' => $file,
                'name' => $result->name,
                'filename' => $result->filename,
                'mime_type' => $result->type,
                'extension' => $result->extname,
                'size' => $result->size,
            ];
            $remoteUrl = 'http://upfile.canliang.wang/bench/' . $result->filepath;
            print_r($aData);
            echo $remoteUrl;
            $this->dealPut($file, $remoteUrl, 'remote');
            $nAttachment = $attachmentModel->create($aData);

            $aInfo = [
                'attachment_id' => $nAttachment['id'],
                'app' => 'culture',
                'info_table' => 'book',
                'info_field' => 'cover',
                'info_id' => $book->code,
            ];
            //print_r($aInfo);exit();
            $attachmentInfoModel->create($aInfo);
        }
        return true;
    }
}
