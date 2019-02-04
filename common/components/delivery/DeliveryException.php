<?php

namespace common\components\delivery;

/**
 * Class DeliveryException
 * @package common\components\delivery
 */
class DeliveryException extends \Exception
{

    const INVALID_RESOURCE_CODE = 0;

    /**
     * @var string $message
     */
    private $message;

    /**
     * @var int $code
     */
    private $code;

    public function __construct(string $message = "", int $code = 0)
    {
        $this->message = 'Invalid source';
        $this->code = self::INVALID_RESOURCE_CODE;
        parent::__construct($message, $code);
    }
}