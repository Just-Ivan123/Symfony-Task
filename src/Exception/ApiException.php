<?php

namespace App\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class APIException extends Exception
{
    private $statusCode;
    private $errors;

    public function __construct(string $message, int $statusCode = 400, ?ConstraintViolationListInterface $violations = null)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errors = $this->formatViolations($violations);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'statusCode' => $this->getStatusCode(),
            'errors' => $this->getErrors(),
        ];
    }


    private function formatViolations(?ConstraintViolationListInterface $violations): array
    {
        if (!$violations) {
            return [];
        }

        $formattedViolations = [];
        foreach ($violations as $violation) {
            $formattedViolations[] = [
                'property' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $formattedViolations;
    }
}