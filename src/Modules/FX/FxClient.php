<?php

namespace Cray\Laravel\Modules\FX;

use Cray\Laravel\Http\CrayClient;

class FxClient
{
    protected CrayClient $client;

    public function __construct(CrayClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get Specific Exchange Rate
     * 
     * @param array $data ['source_currency', 'destination_currency']
     * @return array
     */
    public function rates(array $data): array
    {
        return $this->client->post('/api/v2/merchant/rates', $data);
    }

    /**
     * Get Rates by Destination Currency
     *
     * @param array $data ['destination_currency']
     * @return array
     */
    public function ratesByDestination(array $data): array
    {
        return $this->client->post('/api/v2/merchant/rates/destination', $data);
    }

    /**
     * Generate a Quote
     *
     * @param array $data ['destination_currency', 'source_currency', 'source_amount']
     * @return array
     */
    public function quote(array $data): array
    {
        return $this->client->post('/api/v2/merchant/quote', $data);
    }

    /**
     * Execute Conversion
     *
     * @param array $data ['quote_id']
     * @return array
     */
    public function convert(array $data): array
    {
        return $this->client->post('/api/v2/merchant/conversions/convert', $data);
    }

    /**
     * Query Conversions
     *
     * @return array
     */
    public function conversions(): array
    {
        return $this->client->get('/api/v2/merchant/conversions');
    }
}
