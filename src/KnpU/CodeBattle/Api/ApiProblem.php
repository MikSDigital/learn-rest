<?php

namespace KnpU\CodeBattle\Api;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiProblem
{
    const TYPE_VALIDATION_ERROR = 'validation_error';
    const TYPE_INVALID_REQUEST_BODY_FORMAT = 'invalid_body_format';

    private $statusCode;
    private $type;
    private $title;
    private $extraData = [];

    private static $titles = [
        self::TYPE_VALIDATION_ERROR => 'There was a validation error',
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => 'Invalid JSON format sent',
    ];

    /**
     * ApiProblem constructor.
     *
     * @param int $statusCode
     * @param string $type
     * @throws Exception
     */
    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;
        $this->type = $type;

        if (is_null($this->type)) {
            $this->type = 'about:blank';
            $this->title = isset(Response::$statusTexts[$statusCode])
                ? Response::$statusTexts[$statusCode]
                : 'Unknown status code :(';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new Exception(
                    'No title for type "%s". Did you make it up?',
                    $type
                );
            }

            $this->title = self::$titles[$type];
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->extraData[$key] = $value;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [
            'statusCode' => $this->statusCode,
            'type' => $this->type,
            'title' => $this->title,
        ];

        return array_merge($data, $this->extraData);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}