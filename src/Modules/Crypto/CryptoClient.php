<?php

namespace Cray\Laravel\Modules\Crypto;

use Cray\Laravel\Http\CrayClient;

class CryptoClient
{
    protected CrayClient $client;

    public function __construct(CrayClient $client)
    {
        $this->client = $client;
    }

    /**
     * List supported stablecoin collection assets.
     *
     * @return array
     */
    public function supportedAssets(): array
    {
        return $this->client->get('/api/virtual-accounts/crypto/supported-assets');
    }

    /**
     * Create a crypto vault account with wallet.
     *
     * @param array $data
     * @return array
     */
    public function createVault(array $data): array
    {
        return $this->client->post('/api/accounts/crypto/vault', $data);
    }
}
