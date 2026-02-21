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
        return $this->client->post('/api/rates', $data);
    }

    /**
     * Get Rates by Destination Currency
     *
     * @param array $data ['destination_currency']
     * @return array
     */
    public function ratesByDestination(array $data): array
    {
        return $this->client->post('/api/rates/destination', $data);
    }

    /**
     * Generate a Quote
     *
     * @param array $data ['destination_currency', 'source_currency', 'source_amount']
     * @return array
     */
    public function quote(array $data): array
    {
        return $this->client->post('/api/quote', $data);
    }

    /**
     * Execute Conversion
     *
     * @param array $data ['quote_id']
     * @return array
     */
    public function convert(array $data): array
    {
        return $this->client->post('/api/conversions', $data);
    }

    /**
     * Query Conversions
     *
     * @return array
     */
    public function conversions(): array
    {
        return $this->client->get('/api/conversions');
    }

    /**
     * Dispute Conversion
     *
     * @param string $conversionId
     * @param array $data
     * @return array
     */
    public function disputeConversion(string $conversionId, array $data = []): array
    {
        return $this->client->post("/api/conversions/{$conversionId}/dispute", $data);
    }
}
