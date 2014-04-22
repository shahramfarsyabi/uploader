<?php 

namespace Sh\Library;


class Uploader{
	

	public static $UploadConfig = NULL;
	public static $UpFolder     = NULL;


	public static function _hasfile(){

		if(is_array($_FILES) && count($_FILES) > 0){
			return true;
		}else{
			return false;
		}

	}

	public static function _doupload($Config, $id = NULL){

		$FileList['uploaded'] = array();
		$FileList['errors'] = array();

		self::$UploadConfig = $Config;
		$UploadFolder = "./".$Config['move'];

		if(!is_null($id) && $Config['folderbyid'] == true){
			$UploadFolder = $UploadFolder.$id."/";
		}

		if(!file_exists($UploadFolder)){
			mkdir($UploadFolder);
		}

		

		if($Config["checkfolder"]){

			$FilesInFolder = count(self::CheckFolder($UploadFolder));
			if($FilesInFolder >= $Config['file_num']){
				$FileList['errors'][] = "More than allowed";
			 	return $FileList;
			}		

		}

		$file_input = Uploader::_columnname();	

		foreach($file_input as $k => $v){	

			$File = $_FILES[$v];

			if(is_array($File['tmp_name'])){

				foreach($File['tmp_name'] as $key => $val){

					if($key < $Config['file_num']){

						if($File['tmp_name'][$key] != ''){

							$type = $File['type'][$key];

							if($File['size'][$key] <= $Config['size'] &&  self::FileType($type, $Config) == true){

								if($Config['thisname'] == true){

									$ext = pathinfo($File['name'][$key], PATHINFO_EXTENSION);
									$FileName = $id.".".$ext;

								}else{

									$Config['namebyid'] == true ? $FileName = self::RenameByID($File['name'][$key], $id) : $FileName = $File['name'][$key];

								}
								
								$Destination = $UploadFolder.$FileName;

								if(move_uploaded_file($val, $Destination)){
									$FileList['uploaded'][$v][] = $FileName;

									if($Config['thumbs'] == true){
										self::Resize($UploadFolder, $FileName, $Config);
									}	

								}

							}else{
								$FileList['errors'][] = "FileSize";
							}

						}

					}else{
						$FileList['errors'][] = "NumFile";
					}

				}

			}else if(!is_array($File['tmp_name']) && $File['tmp_name'] != ''){

				$type = $File['type'];	

				if($File['size'] <= $Config['size'] &&  self::FileType($type, $Config) == true){										
					if($Config['thisname'] == true){

						$ext = pathinfo($File['name'], PATHINFO_EXTENSION);
						$FileName = $id.".".$ext;

					}else{

						$Config['namebyid'] == true ? $FileName = self::RenameByID($File['name'], $id) : $FileName = $File['name'];
					}

					$Destination = $UploadFolder.$FileName;

					if(move_uploaded_file($File['tmp_name'], $Destination)){
						$FileList['uploaded'][$v] = $FileName;

						if($Config['thumbs'] == true){
							self::Resize($UploadFolder, $FileName, $Config);
						}							

					}

				}

			}
			
		}		

		return $FileList;
	}

	public static function _columnname(){
		return array_keys($_FILES);
	}

	public static function Resize($UploadFolder , $File ,$Config ){

		$ext = explode('.',$File);
		if(!empty($Config['format'])){
			$FileName = $ext[0].".".$Config['format'];
		}else{
			$FileName = $File;
		}


		foreach($Config['resize'] as $resize){
			$size = explode('x', $resize);
			// Create NewFolder If Directory Not Exists
			if(!file_exists($UploadFolder.$resize)){

				mkdir($UploadFolder.$resize);

			}

			Image::make($UploadFolder.$File)->resize($size[0], $size[1])->save($UploadFolder.$resize."/".$FileName, 72);			
		}

		return true;
	}



	protected static function FileType($type = NULL , $Config = array() ){
		$type = explode("/", $type);
		$file_type = array(
			"image",			
		);		
		$UserTypes = NULL;		
		if(array_key_exists("file_type", $Config)){
			$UserTypes = explode("|", $Config["file_type"]);
		}	

		if(is_array($UserTypes)){
			$file_type = $UserTypes;
		}

		if(in_array($type[0], $file_type)){
			return true;
		}

		return false;
	}

	protected static function RenameByID($File, $id){
		
		$Config = self::$UploadConfig;
		$Folder = "./".$Config['move'].$id."/";
		$x = 0;
		$ext = pathinfo($File, PATHINFO_EXTENSION);

		while($Config['file_num'] > $x){
			
			if(!file_exists($Folder.$id.$x.".".$ext)){
				$File = $id.$x.".".$ext;
				break;
			}

		$x++;}
		
		return $File;
	}

	public static function CheckFolder($UpFolder){
		$result = NULL;
		if(!file_exists($UpFolder)){
			mkdir($UpFolder);
		}	

	    $root = scandir($UpFolder);
	    foreach($root as $value)
	    {
	        if($value === '.' || $value === '..') {continue;}
	        if(is_file("$UpFolder/$value")) {$result[]="$UpFolder/$value";continue;}
	        // foreach(self::CheckFolder("$UpFolder/$value") as $value)
	        // {
	        //     $result[]=$value;
	        // }
	    }
	    return $result; 
	}

}
?>