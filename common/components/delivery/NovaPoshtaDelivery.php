<?php
/**
 * Created by PhpStorm.
 * User: daniil
 * Date: 27.08.18
 * Time: 18:26
 */

namespace common\components\delivery;

use common\components\marketplace\MarketplaceProvider;
use common\models\Orders;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Response;


class NovaPoshtaDelivery extends DeliveryService implements DeliveryServiceInterface
{
    const NP_WAITING_FOR_SENDER = 1;
    const NP_NUMBER_NOT_FOUND = 3;
    const NP_DEPARTURE_IN_CITY_REGION = 4;
    const NP_DEPARTURE_GOES_TO_CITY = 5;
    const NP_DEPARTURE_IN_CITY = 6;
    const NP_ARRIVED_AT_OFFICE_7 = 7;
    const NP_ARRIVED_AT_OFFICE_8 = 8;
    const NP_SENDING_RECEIVED = 9;
    const NP_SENDING_RECEIVED_SMS = 10;
    const NP_SENDING_RECEIVED_MONEY_ISSUED = 11;
    const NP_DEPARTURE_SENT_RECEIVER_FOR_REVIEW = 14;
    const NP_DEPARTURE_IN_CITY_INCITY = 41;
    const NP_ON_WAY_TO_RECIPIENT = 101;
    const NP_REFUSAL_OF_RECIPIENT_102 = 102;
    const NP_REFUSAL_OF_RECIPIENT_103 = 103;
    const NP_ADDRESS_CHANGED = 104;
    const NP_CANCELED_STORAGE = 105;
    const NP_RECEIVED_AND_MONEY_TRANSFER_TTN = 106;
    const NP_CHARGE_FOR_STORAGE = 107;
    const NP_REFUSAL_OF_RECIPIENT_108 = 108;

    const NOVA_POSHTA_STATUSES = [
        self::NP_WAITING_FOR_SENDER => "Нова пошта очікує надходження від відправника",
        self::NP_DEPARTURE_IN_CITY_REGION => "Відправлення у місті ХХXХ. (Статус для міжобласних відправлень)",
        self::NP_DEPARTURE_GOES_TO_CITY => "Відправлення прямує до міста YYYY.",
        self::NP_DEPARTURE_IN_CITY => "Відправлення у місті YYYY, орієнтовна доставка до ВІДДІЛЕННЯ-XXX dd-mm.
Очікуйте додаткове повідомлення про прибуття.",
        self::NP_ARRIVED_AT_OFFICE_7 => "Прибув на відділення",
        self::NP_ARRIVED_AT_OFFICE_8 => "Прибув на відділення",
        self::NP_SENDING_RECEIVED => "Відправлення отримано",
        self::NP_SENDING_RECEIVED_SMS => "Відправлення отримано %DateReceived%.
Протягом доби ви одержите SMS-повідомлення про надходження грошового переказу
та зможете отримати його в касі відділення «Нова пошта». ",
        self::NP_SENDING_RECEIVED_MONEY_ISSUED => "Відправлення отримано %DateReceived%.
Грошовий переказ видано одержувачу.",
        self::NP_DEPARTURE_SENT_RECEIVER_FOR_REVIEW => "Відправлення передано до огляду отримувачу",
        self::NP_DEPARTURE_IN_CITY_INCITY => "Відправлення у місті ХХXХ. (Статус для послуг локал стандарт і локал експрес - доставка в межах міста)",
        self::NP_ON_WAY_TO_RECIPIENT => "На шляху до одержувача",
        self::NP_REFUSAL_OF_RECIPIENT_102 => "Відмова одержувача",
        self::NP_REFUSAL_OF_RECIPIENT_103 => "Відмова одержувача",
        self::NP_ADDRESS_CHANGED => "Змінено адресу",
        self::NP_CANCELED_STORAGE => "Припинено зберігання",
        self::NP_RECEIVED_AND_MONEY_TRANSFER_TTN => "Одержано і є ТТН грошовий переказ",
        self::NP_CHARGE_FOR_STORAGE => "Нараховується плата за зберігання",
        self::NP_REFUSAL_OF_RECIPIENT_108 => "Відмова одержувача",
    ];
    const HB_TRANSFERRED_TO_DELIVERY_SERVICE_STATUS = 10;
    const HB_DELIVERED_STATUS = 5;
    const HB_NOT_PICK_UP_PARCEL_STATUS = 11;
    const HB_REFUSED_GOODS_STATUS = 12;
    const HB_DONE_STATUS = 6;

    const HB_DELIVERED_STATUSES_INDEX = 0;
    const HB_REFUSED_GOODS_STATUSES_INDEX = 1;
    const HB_NOT_PICK_UP_PARCEL_STATUSES_INDEX = 2;
    const HB_TRANSFERRED_TO_DELIVERY_SERVICE_STATUSES_INDEX = 3;
    const HB_DONE_STATUSES_INDEX = 4;

    /**
     * @return bool
     */
    public function syncDeliveryStatuses(): bool
    {
        return parent::syncDeliveryStatuses();
    }

    /**
     * @param array $orders
     *
     * @return array
     */

    public function checkStatuses($orders): array
    {
        $mailStatuses = [];

        $json = new \stdClass();

        $json->apiKey = $this->getApiKey();
        $json->modelName = "TrackingDocument";
        $json->calledMethod = "getStatusDocuments";

        foreach ($orders as $order) {
            $json->methodProperties->Documents[] = [
                "DocumentNumber" => $order->ttn,
                "Phone" => ""
            ];
        }

        $json = Json::encode($json);

        $result = $this->getApiResponse($json);

        if ($result["success"] == 'true') {
            $result = $result["data"];
            if (!empty($result)) {
                foreach ($result as $item) {
                    $mailStatuses[] = [
                        "ttn" => $item["Number"],
                        "status" => $item["Status"],
                        "statusCode" => $item["StatusCode"],
                    ];
                }

            }
        }

        return $mailStatuses;
    }

    /**
     * @param string $json
     *
     * @return mixed
     */
    public function getApiResponse(string $json)
    {
        $client = new Client();
        /** @var Response $response */
        $response = $client->createRequest()
            ->addHeaders(['content-type' => 'application/json'])
            ->setMethod('POST')
            ->setUrl($this->getUrl())
            ->setContent($json)
            ->setFormat(Client::FORMAT_JSON)
            ->send();

        return Json::decode($response->getContent());
    }

    /**
     * @param array $mailStatuses
     *
     * @return array
     */
    public function mapStatuses($mailStatuses): array
    {
        $orderStatuses = [];
        $NPStatuses = [
            self::HB_DELIVERED_STATUSES_INDEX=>
            [
                self::NP_DEPARTURE_IN_CITY_REGION,
                self::NP_DEPARTURE_IN_CITY_INCITY,
                self::NP_DEPARTURE_GOES_TO_CITY,
                self::NP_DEPARTURE_IN_CITY,
                self::NP_ARRIVED_AT_OFFICE_7,
                self::NP_ARRIVED_AT_OFFICE_8,
                self::NP_DEPARTURE_SENT_RECEIVER_FOR_REVIEW,
                self::NP_ON_WAY_TO_RECIPIENT,
                self::NP_ADDRESS_CHANGED,
                self::NP_CHARGE_FOR_STORAGE
            ],
            self::HB_REFUSED_GOODS_STATUSES_INDEX=>
            [
                self::NP_REFUSAL_OF_RECIPIENT_102,
                self::NP_REFUSAL_OF_RECIPIENT_103,
                self::NP_REFUSAL_OF_RECIPIENT_108
            ],
            self::HB_NOT_PICK_UP_PARCEL_STATUSES_INDEX=>
            [
                self::NP_CANCELED_STORAGE
            ],
            self::HB_TRANSFERRED_TO_DELIVERY_SERVICE_STATUSES_INDEX=>
            [
                self::NP_WAITING_FOR_SENDER
            ],
            self::HB_DONE_STATUSES_INDEX=>
            [
                self::NP_SENDING_RECEIVED,
                self::NP_SENDING_RECEIVED_SMS,
                self::NP_SENDING_RECEIVED_MONEY_ISSUED,
                self::NP_RECEIVED_AND_MONEY_TRANSFER_TTN
            ]
        ];
        foreach ($mailStatuses as $mailStatus) {
            if (in_array($mailStatus["statusCode"], $NPStatuses[self::HB_DELIVERED_STATUSES_INDEX])) {
                $orderStatuses[] = [
                    "ttn" => $mailStatus["ttn"],
                    "status" => self::HB_DELIVERED_STATUS
                ];
            }
            if (in_array($mailStatus["statusCode"], $NPStatuses[self::HB_REFUSED_GOODS_STATUSES_INDEX])) {
                $orderStatuses[] = [
                    "ttn" => $mailStatus["ttn"],
                    "status" => self::HB_REFUSED_GOODS_STATUS
                ];
            }
            if (in_array($mailStatus["statusCode"], $NPStatuses[self::HB_NOT_PICK_UP_PARCEL_STATUSES_INDEX])) {
                $orderStatuses[] = [
                    "ttn" => $mailStatus["ttn"],
                    "status" => self::HB_NOT_PICK_UP_PARCEL_STATUS
                ];
            }
            if (in_array($mailStatus["statusCode"], $NPStatuses[self::HB_TRANSFERRED_TO_DELIVERY_SERVICE_STATUSES_INDEX])) {
                $orderStatuses[] = [
                    "ttn" => $mailStatus["ttn"],
                    "status" => self::HB_TRANSFERRED_TO_DELIVERY_SERVICE_STATUS
                ];
            }
            if (in_array($mailStatus["statusCode"], $NPStatuses[self::HB_DONE_STATUSES_INDEX])) {
                $orderStatuses[] = [
                    "ttn" => $mailStatus["ttn"],
                    "status" => self::HB_DONE_STATUS
                ];
            }
        }
        return $orderStatuses;
    }

    /**
     * @return array
     */
    protected function getOrdersForSync(): array
    {
        $ts = time() - $this->getSyncPeriod();
        $orders = Orders::find()->select("ttn,id, status, marketplace_name")
            ->where("ttn IS NOT NULL")
            ->andWhere(["status" => [
                self::HB_TRANSFERRED_TO_DELIVERY_SERVICE_STATUS,
                self::HB_DELIVERED_STATUS,
            ]])
            //->andWhere(["<>", "marketplace_name", MarketplaceProvider::MP_ROZETKA])
            ->andWhere([">", "created_at", $ts])
            ->limit(100)
            ->all();

        return $orders ?? [];
    }
}