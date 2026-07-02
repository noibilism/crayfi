<?php

namespace Cray\Laravel\Modules\CryptoPayouts;

use Cray\Laravel\Http\CrayClient;

class CryptoPayoutClient
{
    protected CrayClient $client;

    public function __construct(CrayClient $client)
    {
        $this->client = $client;
    }

    /**
     * List supported stablecoin payout assets.
     *
     * @return array
     */
    public function supportedAssets(): array
    {
        return $this->client->get('/api/virtual-accounts/crypto/supported-assets');
    }

    /**
     * Add an on-chain wallet beneficiary.
     *
     * @param array $data
     * @return array
     */
    public function addBeneficiary(array $data): array
    {
        return $this->client->post('/api/payout/crypto/beneficiaries', $data);
    }

    /**
     * Initiate a stablecoin payout.
     *
     * @param array $data
     * @return array
     */
    public function initiatePayout(array $data): array
    {
        return $this->client->post('/api/payout/crypto/initiate-payout', $data);
    }
}
