<?php namespace Shahram\Uploader;

use Illuminate\Support\ServiceProvider;

class UploaderServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('Uploader', function()
        {
            return new \Shahram\Uploader\Uploader();
        });
    }

}