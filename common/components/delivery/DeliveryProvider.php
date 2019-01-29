<?php
/**
 * Created by PhpStorm.
 * User: daniil
 * Date: 28.08.18
 * Time: 11:11
 */

namespace common\components\delivery;


use yii\base\Component;

class DeliveryProvider extends Component
{
    const DELIVERY_NP = 'NovaPoshta';

    public $services = [];

    public $deliveryCompanyIdMap = [];

    public $synchronisationDelays = [];

    public $sourcesList = [];
    /**
     * @var string
     */
    private $source;
    /**
     * @var DeliveryServiceInterface
     */
    private $ds;
    /**
     * @var
     */
    private $conf;


    /**
     * @return DeliveryServiceInterface
     * @throws DeliveryException
     * @throws \yii\base\InvalidConfigException
     */
    public function create(): DeliveryServiceInterface
    {
        if (empty($this->source)) {
            throw new DeliveryException();
        }

        if ($this->ds) {
            return $this->ds;
        }
        $this->ds = \Yii::createObject($this->conf['class'], [
            $this->conf['api_key'],
            $this->conf['url'],
            $this->conf['sync_period']
        ]);
        $this->ds->setName($this->source);

        return $this->ds;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source)
    {
        $this->source = $source;
        $this->ds = null;

        $this->initConfig();
    }

    /**
     * @return \Generator
     * @throws DeliveryException
     * @throws \yii\base\InvalidConfigException
     */
    public function deliveryServicesList(): \Generator
    {
        $provider = new self();

        $provider->services = $this->services;

        foreach (array_keys($this->services) as $service) {
            $provider->setSource($service);
            yield $provider->create();
        }
    }

    /**
     * @return void
     */
    private function initConfig():void
    {
        $this->conf = $this->services[$this->source];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getConf(string $key)
    {
        return $this->conf[$key];
    }

}