<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImageService
{
    private string $apiKey;
    private HttpClientInterface $httpClient;

    public function __construct(
        string $imgApiKey)
    {
        $this->apiKey = $imgApiKey;
        $this->httpClient = HttpClient::create();
    }

    public function upload(UploadedFile $file): array
    {
        $imageData = base64_encode(file_get_contents($file->getPathname()));

        $response = $this->httpClient->request('POST', 'https://api.imgbb.com/1/upload', [
            'body' => http_build_query([
                'key' => $this->apiKey,
                'image' => $imageData,
                'name' => $file->getClientOriginalName(),
            ]),
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        $data = $response->toArray();

        if (!isset($data['success']) || !$data['success']) {
            throw new \Exception($data['error']['message'] ?? 'Failed to upload image');
        }

        return [
            'display_url' => $data['data']['display_url'],
        ];
    }

    public function getPhoto(User $user, $photoFile): User {
        if ($photoFile !== null) {
            $result = $this->upload($photoFile);
            $user->setPhoto($result['display_url']);
        }
        return $user;
    }
}
