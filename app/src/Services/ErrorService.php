<?php

namespace App\Services;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ErrorService
{
    public function getMessages($entity, ValidatorInterface $validator): array
    {
        $errors = $validator->validate($entity);
        $errorMsg = [];

        for ($i = 0; ; $i++) {
            if ($errors->has($i)) {
                $error = $errors->get($i);
                $errorMsg['type'] = "https://localhost/validation-error";
                $errorMsg['title'] = "Your request parameters didn't validate.";
                $errorMsg['invalid-params'][] = [
                    'name' => $error->getPropertyPath(),
                    'reason' => $error->getMessage(),
                ];
                continue;
            }
            break;
        }

        return $errorMsg;
    }
}