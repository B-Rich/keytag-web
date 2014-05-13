<?php

/**
 * This class is to  be used for encoding, decoding and uploading Video to Amazon S3
 *
 * @author Alina Vasilevich
 */
class FileHandler {
    public $fid; // uploaded file ID
    
    private $uploadVideoFolder;
    private $uploadImageFolder;
            
    private $convertVideoFolder;
    private $convertVideoWidth;
    private $convertVideoHeight;
    private $uploadedFile;
    
    public $rootPath;

    public function __construct($id) {
        
        $this->uploadedFile = $id ? File::model()->findByPk($id): new File();
        
        $this->rootPath=Yii::getPathOfAlias('webroot');
         
        $this->uploadVideoFolder = $this->rootPath.Yii::app()->params['commonMedia']['videoUploadOriginalPath'];
        $this->uploadImageFolder = $this->rootPath.Yii::app()->params['commonMedia']['imageUploadPath'];
            
        $this->convertVideoFolder = $this->rootPath.Yii::app()->params['commonMedia']['videoUploadConvertedPath'];
        $this->convertVideoWidth = Yii::app()->params['commonMedia']['convertWidth'];
        $this->convertVideoHeight = Yii::app()->params['commonMedia']['convertHeight'];              
    } 

    /*
     * Common "blackbox" function for video uploading, converting, sending to Amazon S3
     * 
     * @fileData - array from $_FILES
     */
    public function handleVideo($fileData) {
        $this->fid = $this->uploadVideo($fileData); 
        
        if ($this->fid) {
            try {
                $this->convertVideo($this->fid);
            } catch (CException $e) {
                echo $e->getMessage();
                return false;
            }    

            try {
                $this->sendVideotoAmazonS3($this->fid);
            } catch (CException $e) {
                echo $e->getMessage();
                return false;
            }
            return $this->fid;
        } else {
            return false;
        }
    }

    /*
     * Common "blackbox" function for image uploading
     * 
     * @fileData - array from $_FILES
     */
    public function handleImage($fileData) { 
        $this->fid = $this->uploadImage($fileData); 
       
        if ($this->fid) {
            return $this->fid;
        } else {
            return false;
        }
    }
    
    public function uploadVideo($uploadedFileArray, $safeMode=true) {
        $this->uploadedFile->instance = new CUploadedFile($uploadedFileArray['name'], $uploadedFileArray['tmp_name'],$uploadedFileArray['type'], $uploadedFileArray['size'], $uploadedFileArray['error']);
        $finalFileName = self::renameDuplicates($this->uploadedFile->instance, $this->uploadVideoFolder);
        if ($finalFileName) {
            $saved = false;
            $path = $this->uploadVideoFolder.$finalFileName;
            if ($safeMode) {
                $saved = $this->uploadedFile->instance->saveAs($path);
            }
            else {
                $saved = rename($this->uploadedFile->instance->tempName, $path);
            }
            if ($saved) {
                @chmod($path, 0644);
                return $this->setFileInfo(Yii::app()->params['commonMedia']['videoUploadOriginalPath'].$finalFileName, $finalFileName); 
            }
        }
    }

    public function uploadImage($uploadedFileArray, $safeMode=true) {
        $this->uploadedFile->instance = new CUploadedFile($uploadedFileArray['name'], $uploadedFileArray['tmp_name'],$uploadedFileArray['type'], $uploadedFileArray['size'], $uploadedFileArray['error']) ;
        $finalFileName = self::renameDuplicates($this->uploadedFile->instance, $this->uploadImageFolder);
        if ($finalFileName) {
            $saved = false;
            $path = $this->uploadImageFolder.$finalFileName;
            if ($safeMode) {
                $saved = $this->uploadedFile->instance->saveAs($path);
            }
            else {
                $saved = rename($this->uploadedFile->instance->tempName, $path);
            }
            if ($saved) {
                @chmod($path, 0644);
                return $this->setFileInfo(Yii::app()->params['commonMedia']['imageUploadPath'].$finalFileName, $finalFileName); 
            } 
        }
    }        

    public function convertVideo($fileID) {
        $fileData = $this->getFileInfo($fileID);
        $fileName = substr($fileData->OriginalPath, strripos($fileData->OriginalPath, '/')+1);
        $convertedFileInfo = FileConverter::encodeOriginalVideo($fileName, $this->uploadVideoFolder, $this->convertVideoFolder, $this->convertVideoWidth, $this->convertVideoHeight);
        
        if ($convertedFileInfo) {                    
            $this->updateFileInfo($fileID, $convertedFileInfo);
        }
    }

    public function sendVideotoAmazonS3($fileID) {
        $fileData = $this->getFileInfo($fileID);
        $fileName = substr($fileData->ConvertedPath, strripos($fileData->ConvertedPath, '/')+1);
        
        $amazonFileInfo = FileAmazonUploader::uploadToAmazonS3($fileName, $this->rootPath, Yii::app()->s3->uploadPath. $fileName, Yii::app()->s3->bucket);
        if ($amazonFileInfo) {
            $this->updateFileInfo($fileID, $amazonFileInfo);
            //$this->deleteLocalVideo($fileID); //TODO implement
        }
    }
    
    public function getFileInfo($fileID) {
        return File::model()->findByPk($fileID);
    }
    
    private function setFileInfo ($path, $fileName) { 
        $this->uploadedFile->Name = $fileName;
        $this->uploadedFile->OriginalPath = $path;
        $this->uploadedFile->OriginalMimeType = $this->uploadedFile->instance->type;
        $this->uploadedFile->OriginalFileSize = $this->uploadedFile->instance->size;
        $this->uploadedFile->SetUpdateInfo();

        if ($this->uploadedFile->save()) {
            return $this->uploadedFile->ID;
        } else {
            return false;
        }
    }
   
    private function updateFileInfo($fileID, $attributesValue) {
        
        $this->uploadedFile->SetUpdateInfo();        
        $this->uploadedFile->updateByPk($fileID, $attributesValue);
    }

    public function validate($uploadedFile) {
        if (file_exists($uploadFolder.$uploadedFile->name)) {
            $originalFileName = pathinfo($uploadedFile->name, PATHINFO_FILENAME);
            $fileExtension =  $uploadedFile->extensionName;
            $fileCounter = 0;
            while (file_exists($uploadFolder.$finalFileName )) {
                $finalFileName = $originalFileName . '_' . $fileCounter++ . '.' . $fileExtension;        
            }
        } else {
            $finalFileName = $uploadedFile->name;
        }

        return $finalFileName;
    }
    
    private function renameDuplicates($uploadedFile, $uploadFolder) {

        if (file_exists($uploadFolder.$uploadedFile->name)) {
            $originalFileName = pathinfo($uploadedFile->name, PATHINFO_FILENAME);
            $fileExtension =  $uploadedFile->extensionName;
            $fileCounter = 0;
            while (file_exists($uploadFolder.$finalFileName )) {
                $finalFileName = $originalFileName . '_' . $fileCounter++ . '.' . $fileExtension;        
            }
        } else {
            $finalFileName = $uploadedFile->name;
        }

        return $finalFileName;
    }
}

?>