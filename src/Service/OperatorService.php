<?php

namespace App\Service;

use App\Domain\Entity\File;
use App\Entity\Instance;
use App\Enum\VpnService;
use App\Exception\OperatorException;
use App\Repository\OperatorRepository;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class OperatorService
{
    public static function createClient(VpnService $service, Instance $instance): File
    {
        $secret = OperatorRepository::getOperatorSecret();
        $url = $_ENV['OPERATOR_URL'] . '/client/' . $service->value . '/' . $instance->id;
        $client = new Client([
            'http_errors' => false
        ]);


        $response = $client->post($url, [
            'headers' => [
                'Authorization' => $secret
            ]
        ]);

        if ($response->getStatusCode() !== Response::HTTP_CREATED) {
            throw new OperatorException();
        }

        $data = json_decode($response->getBody()->getContents(), true);

        $id = $data['id'];
        $filename = $data['fileName'];
        $content = $data['config'];

        $file = new File();
        $file->name = str_replace($id, 'client', $filename);
        $file->content = base64_decode($content);
        return $file;
    }
}