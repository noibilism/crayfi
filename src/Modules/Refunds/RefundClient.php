<?php

namespace Cray\Laravel\Modules\Refunds;

use Cray\Laravel\Http\CrayClient;

class RefundClient
{
    protected CrayClient $client;

    public function __construct(CrayClient $client)
    {
        $this->client = $client;
    }

    /**
     * Initiate Refund
     * 
     * @param array $data ['pan', 'subaccount_id', 'amount' (optional)]
     * @return array
     */
    public function initiate(array $data): array
    {
        return $this->client->post('/api/v2/refund/initiate', $data);
    }

    /**
     * Check Refund Status
     *
     * @param string $reference
     * @return array
     */
    public function query(string $reference): array
    {
        return $this->client->get("/api/v2/refund/query/{$reference}");
    }
}
