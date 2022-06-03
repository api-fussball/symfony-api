<?php declare(strict_types=1);

namespace App\Component\Service\Exception;

class XpathNotFound extends \Exception
{
    public function __construct(string $message = "Not found")
    {
        parent::__construct($message);
    }
}
