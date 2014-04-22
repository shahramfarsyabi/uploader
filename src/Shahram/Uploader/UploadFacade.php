<?php 
namespace Shahram\Uploader;

use Illuminate\Support\Facades\Facade;

class UploadFacade extends Facade {

    protected static function getFacadeAccessor() { return 'Uploader'; }

}

?>