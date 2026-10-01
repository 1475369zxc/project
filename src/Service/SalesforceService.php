<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SalesforceService
{
    private ?string $accessToken = null;
    private ?string $instanceUrl = null;

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $clientId,
        private string $clientSecret,
        private string $loginUrl,
    ) {}

    public function createAccountWithContact(
        string $pastCompany,
        string $lastName,
        string $email,
        ?string $pastWork = null,
        ?string $phone = null,
    ): array {

        $payload = [
            'allOrNone' => true,
            'compositeRequest' => [
                [
                    'method' => 'POST',
                    'url' => '/services/data/v59.0/sobjects/Account',
                    'referenceId' => 'newAccount',
                    'body' => array_filter([
                        'Name' => $pastCompany,
                    ]),
                ],[
                    'method' => 'POST',
                    'url' => '/services/data/v59.0/sobjects/Contact',
                    'referenceId' => 'newContact',
                    'body' => array_filter([
                        'LastName' => $lastName,
                        'Email' => $email,
                        'Phone' => $phone,
                        'Title' => $pastWork,
                        'AccountId' => '@{newAccount.id}',
                    ]),
                ],
            ],
        ];

        $data = $this->sendRequest($payload);


        return [
            'account_id' => $data['compositeResponse'][0]['body']['id'] ?? null,
            'contact_id' => $data['compositeResponse'][1]['body']['id'] ?? null,
        ];
    }

    public function updateAccountWithContact(
        string $accountId,
        string $contactId,
        string $pastCompany,
        string $lastName,
        string $email,
        ?string $pastWork = null,
        ?string $phone = null,
    ): void {

        $payload = [
            'allOrNone' => true,
            'compositeRequest' => [
                [
                    'method' => 'PATCH',
                    'url' => '/services/data/v59.0/sobjects/Account/' . $accountId,
                    'referenceId' => 'updateAccount',
                    'body' => array_filter([
                        'Name' => $pastCompany,
                    ]),
                ],
                [
                    'method' => 'PATCH',
                    'url' => '/services/data/v59.0/sobjects/Contact/' . $contactId,
                    'referenceId' => 'updateContact',
                    'body' => array_filter([
                        'LastName' => $lastName,
                        'Email' => $email,
                        'Phone' => $phone,
                        'Title' => $pastWork,
                    ]),
                ],
            ],
        ];

        $this->sendRequest($payload);
    }

    public function getAccessToken(): string
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        $response = $this->httpClient->request('POST', $this->loginUrl . '/services/oauth2/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ],
        ]);

        $data = $response->toArray(false);

        if (!isset($data['access_token'], $data['instance_url'])) {
            throw new \RuntimeException('Salesforce auth failed: ' . json_encode($data));
        }

        $this->accessToken = $data['access_token'];
        $this->instanceUrl = $data['instance_url'];

        return $this->accessToken;
    }

    private function sendRequest(array $payload): array
    {
        $token = $this->getAccessToken();

        $response = $this->httpClient->request('POST',
            $this->instanceUrl . '/services/data/v59.0/composite',
            [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'json' => $payload,
            ]
        );

        if ($response->getStatusCode() >= 300) {
            throw new \RuntimeException('Salesforce API error: ' . $response->getContent(false));
        }

        return $response->toArray(false);
    }
}
