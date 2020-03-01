<?php

namespace App\Service;

use App\Enum\Admin\ContentTypes;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JSONAPIService
{
    public function requestHasIDField(Request $request): bool
    {
        if(strpos($request->headers->get('Content-Type'), ContentTypes::JSON) === false) {
            return false;
        }

        $data = json_decode($request->getContent(), true);

        return array_key_exists('id', $data);
    }

    public function makeHTTPJSONResponse(int $statusCode): JsonResponse
    {
        return new JsonResponse([
            'message'=> Response::$statusTexts[$statusCode]],
            $statusCode);
    }

    public function getIDFromRequestBody(Request $request): string
    {
        $data = json_decode($request->getContent(), true);

        return (string)$data['id'];
    }
}