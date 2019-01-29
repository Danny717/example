<?php
namespace common\components\delivery;

interface DeliveryServiceInterface
{
    /**
     * @return bool
     */
    public function syncDeliveryStatuses(): bool;

    /**
     * @param array $orders
     *
     * @return array
     */

    public function checkStatuses($orders): array;

    /**
     * @param array $statuses
     *
     * @return array
     */
    public function mapStatuses($statuses): array;

    /**
     * @param string $json
     *
     * @return mixed
    */
    public function getApiResponse(string $json);


}