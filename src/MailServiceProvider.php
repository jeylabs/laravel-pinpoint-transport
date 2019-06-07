<?php

namespace Jeylabs\PinpointTransport;

use Illuminate\Mail\MailServiceProvider as MailProvider;
use Jeylabs\PinpointTransport\Mail\PinpointAddedTransportManager;

class MailServiceProvider extends MailProvider
{
    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        $this->app->singleton('swift.transport', function () {
            return new PinpointAddedTransportManager($this->app);
        });
    }
}
