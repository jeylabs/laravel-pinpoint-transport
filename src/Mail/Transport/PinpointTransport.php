<?php

namespace Jeylabs\PinpointTransport\Mail\Transport;

use Aws\PinpointEmail\PinpointEmailClient;
use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class PinpointTransport extends Transport
{
    /**
     * The Amazon Pinpoint instance.
     *
     * @var PinpointEmailClient $pinpoint
     */
    protected $pinpoint;

    /**
     * The Amazon Pinpoint transmission options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Create a new Pinpoint transport instance.
     *
     * @param PinpointEmailClient $pinpoint
     * @param array $options
     * @return void
     */
    public function __construct(PinpointEmailClient $pinpoint, $options = [])
    {
        $this->pinpoint = $pinpoint;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $result = $this->pinpoint->sendEmail([
            'Content' => [
                'Raw' => [
                    'Data' => $message->toString(),
                ],
            ],
            'Destination' => [
                'BccAddresses' => array_keys($message->getBcc() ?: []),
                'CcAddresses' => array_keys($message->getCc() ?: []),
                'ToAddresses' => array_keys($message->getTo()),
            ],
            'FeedbackForwardingEmailAddress' => key($message->getSender() ?: $message->getFrom()),
            'FromEmailAddress' => key($message->getSender() ?: $message->getFrom()),
            'ReplyToAddresses' => [key($message->getSender() ?: $message->getFrom())],
        ]);

        $message->getHeaders()->addTextHeader('X-Pinpoint-Message-ID', $result->get('MessageId'));

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get the Amazon Pinpoint client for the PinpointTransport instance.
     *
     * @return PinpointEmailClient
     */
    public function pinpoint()
    {
        return $this->pinpoint;
    }

    /**
     * Get the transmission options being used by the transport.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the transmission options being used by the transport.
     *
     * @param array $options
     * @return array
     */
    public function setOptions(array $options)
    {
        return $this->options = $options;
    }
}