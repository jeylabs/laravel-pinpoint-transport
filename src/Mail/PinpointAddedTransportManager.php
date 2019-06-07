<?php

namespace Jeylabs\PinpointTransport\Mail;


use Aws\PinpointEmail\PinpointEmailClient;
use Illuminate\Mail\TransportManager;
use Illuminate\Support\Arr;
use Jeylabs\PinpointTransport\Mail\Transport\PinpointTransport;

class PinpointAddedTransportManager extends TransportManager
{
    /**
     * Create an instance of the Amazon Pinpoint Swift Transport driver.
     *
     * @return PinpointTransport
     */
    protected function createPinpointDriver()
    {
        $config = array_merge($this->app['config']->get('services.pinpoint', []), [
            'version' => 'latest',
        ]);

        return new PinpointTransport(
            new PinpointEmailClient($this->addPinpointCredentials($config)),
            $config['options'] ?? []
        );
    }

    /**
     * Add the Pinpoint credentials to the configuration array.
     *
     * @param array $config
     * @return array
     */
    protected function addPinpointCredentials(array $config)
    {
        if ($config['key'] && $config['secret']) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return $config;
    }
}