<?php

namespace common\components\delivery;
use yii\httpclient\Request;


abstract class DeliveryService implements DeliveryServiceInterface
{

    /**
     * @var Request
     */
    protected $client;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $syncPeriod;

    const NP_DELIVERY_TYPE = 0;

    /**
     * DeliveryService constructor.
     * @param $apiKey
     * @param $url
     * @param $syncPeriod
     */
    final public function __construct($apiKey, $url, $syncPeriod)
    {
        $this->url = $url;
        $this->apiKey = $apiKey;
        $this->syncPeriod = $syncPeriod;
    }

    /**
     * @param array $orders
     *
     * @return array
     */

    abstract public function checkStatuses($orders): array;

    /**
     * @param array $statuses
     *
     * @return array
     */
    abstract public function mapStatuses($statuses): array;

    /**
     * @return array
     */

    abstract protected  function getOrdersForSync():array;

    /**
     * @param string $json
     *
     * @return mixed
     */
    abstract public function getApiResponse(string $json);

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl():string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getApiKey():string
    {
        return $this->apiKey;
    }

    /**
     * @return int
     */
    public function getSyncPeriod():int
    {
        return (int)$this->syncPeriod;
    }

    /**
     * @return bool
     */
    public function syncDeliveryStatuses(): bool
    {
        $sync = false;
        $orders = $this->getOrdersForSync();

        if(!empty($orders)){
            $mailStatuses = $this->checkStatuses($orders);

            if(!empty($mailStatuses)){
                $myStatuses = $this->mapStatuses($mailStatuses);

                $ttns = [];
                foreach ($myStatuses as $myStatus){
                    $ttns[] = $myStatus["ttn"];
                }
                $orders = Orders::findAll(["ttn"=> $ttns]);
                $ordersByTtnsKey = [];
                foreach($orders as $order){
                    $ordersByTtnsKey[$order->ttn] = $order;
                }


                foreach ($myStatuses as $status){

                    $order = $ordersByTtnsKey[$status["ttn"]];
                    if($order->status != $status["status"]){

                        if($order->marketplace_name == MarketplaceProvider::MP_MARKET){
                            $orderStatusesLog = new OrderStatusLog();
                            $orderStatusesLogService = new OrderStatusLogService($orderStatusesLog);
                            $orderStatusesLogService->setMarketWrongStatus($order,$status["status"]);
                        }
                        else {
                            $oldStatus= $order->status;
                            $order->status = $status["status"];
                            try{
                                $order->save();
                                echo "Changed from ".$oldStatus." to ".$order->status."\n<br>";
                            }
                            catch(\Exception $e){
                                echo $e->getMessage()."\n<br>";
                            }
                        }
                    }
                }
            }
            $sync = true;
        }
        return $sync;
    }
}