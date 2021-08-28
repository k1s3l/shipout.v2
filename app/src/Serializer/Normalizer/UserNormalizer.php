<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserNormalizer extends ObjectNormalizer
{
    public function normalize($object, string $format = null, array $context = [])
    {
        $context = $context + [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => fn ($object, $format, $context) => $object->getId(),
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'salt', 'user', 'fullName'],
        ];

        $data = parent::normalize($object, $format, $context);

        return $data;
    }
}
