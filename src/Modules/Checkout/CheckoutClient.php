<?php

namespace Cray\Laravel\Modules\Checkout;

use Cray\Laravel\Http\CrayClient;

class CheckoutClient
{
    protected CrayClient $client;

    public function __construct(CrayClient $client)
    {
        $this->client = $client;
    }

    /**
     * Initialize a hosted checkout payment.
     *
     * @param array $data
     * @return array
     */
    public function initialize(array $data): array
    {
        return $this->client->post('/api/checkout/initialize', $data);
    }

    /**
     * Query a hosted checkout payment.
     *
     * @param string $reference
     * @return array
     */
    public function query(string $reference): array
    {
        return $this->client->get("/api/checkout/query/{$reference}");
    }
}
