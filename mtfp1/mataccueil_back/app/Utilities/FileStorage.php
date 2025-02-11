<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Image,Str;

class FileStorage
{
    static public function setFile($disk, $file, $directory="",$name=""){

        $directory_name=$directory;

        $directory_exist=Storage::disk($disk)->exists($directory_name);

            if(!$directory_exist) Storage::disk($disk)->makeDirectory($directory_name);

            $pre_filename= $name=="" || $name==null?$file->getFilename():Str::slug($name);

            $filename=$pre_filename.'.'.$file->getClientOriginalExtension();

        if (!Storage::disk($disk)->exists($directory_name!="" || $directory_name!=null?$directory_name."/".$filename:$filename)) {

               Storage::disk($disk)->put($directory_name!="" || $directory_name!=null?$directory_name."/".$filename:$filename,  File::get($file));
        }

        return $filename;
    }

    static public function deleteFile($disk,$file,$directory=""){

      $file_exist=Storage::disk($disk)->exists($directory!=""|| $directory!=null?$directory.'/'.$file:$file);

      if($file_exist){
        Storage::disk($disk)->delete($directory!=""|| $directory!=null?$directory.'/'.$file:$file);
        } 
    }

}
