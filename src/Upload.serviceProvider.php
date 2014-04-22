<?php namespace Sh\ServiceProvider;

use Illuminate\Support\ServiceProvider;

class UploaderServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('Uploader', function()
        {
            return new \Sh\Library\Uploader();
        });
    }

}