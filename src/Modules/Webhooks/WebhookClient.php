<?php

namespace Cray\Laravel\Modules\Webhooks;

use Cray\Laravel\Http\CrayClient;

class WebhookClient
{
    protected CrayClient $client;

    public function __construct(CrayClient $client)
    {
        $this->client = $client;
    }

    /**
     * List failed payout webhook deliveries.
     *
     * @return array
     */
    public function failedPayoutWebhooks(): array
    {
        return $this->client->get('/api/payout/failedWebhook');
    }

    /**
     * Retry a failed payout webhook delivery.
     *
     * @param string $webhookId
     * @return array
     */
    public function retryFailedPayoutWebhook(string $webhookId): array
    {
        return $this->client->get("/api/payout/failedWebhook/{$webhookId}");
    }
}
