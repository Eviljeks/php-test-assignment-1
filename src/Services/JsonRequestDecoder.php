<?php

declare(strict_types=1);

namespace App\Services;

use App\Exception\JsonDecodeException;
use JsonException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class JsonRequestDecoder
{
    public function decodeRequest(Request $request): ParameterBag
    {
        $content = $request->getContent();
        try {
            $parameters = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JsonDecodeException(sprintf('Decode exception whilde decoding content: "%s"', $content));
        }

        if (!is_array($parameters)) {
            throw new JsonDecodeException(sprintf('Not array was provided: "%s"', $content));
        }

        return new ParameterBag($parameters);
    }
}