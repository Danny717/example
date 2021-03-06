<?php

namespace common\models;

use Yii;
use DiDom\Document;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $title
 * @property string $amazon_link
 * @property string $target_link
 * @property string $walmart_link
 * @property string $hayneedle_link
 * @property string $waifair_link
 * @property string $amazon_price
 * @property string $target_price
 * @property string $walmart_price
 * @property string $hayneedle_price
 * @property string $waifair_price
 * @property string $update_time
 * @property string $img
 * @property string $asin
 * @property int $buybox
 * @property int $availability
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['update_time'], 'safe'],
            [['buybox', 'availability'], 'integer'],
            [['title', 'amazon_link', 'target_link', 'walmart_link', 'hayneedle_link', 'waifair_link', 'amazon_price', 'target_price', 'walmart_price', 'hayneedle_price', 'waifair_price', 'img', 'asin'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'amazon_link' => 'Amazon Link',
            'target_link' => 'Target Link',
            'walmart_link' => 'Walmart Link',
            'hayneedle_link' => 'Hayneedle Link',
            'waifair_link' => 'Waifair Link',
            'amazon_price' => 'Amazon Price',
            'target_price' => 'Target Price',
            'walmart_price' => 'Walmart Price',
            'hayneedle_price' => 'Hayneedle Price',
            'waifair_price' => 'Waifair Price',
            'update_time' => 'Update Time',
            'img' => 'Img',
            'asin' => 'Asin',
            'buybox' => 'Buybox',
            'availability' => 'Availability',
        ];
    }

    public function beforeSave($insert)
    {
        $this->update_time = date('Y-m-d H:i:s');

        $url = $this->walmart_link;
        $document = new Document($url, true);
        $priceWalmart = $document->find('span.price-group')[0];
        $this->walmart_price = $priceWalmart->text();

        $url = $this->amazon_link;
        $document = new Document($url, true);
        $priceAmazon = $document->find('#olp-upd-new')[0];
        $amazon = $priceAmazon->html();

        //asin
        $ths = $document->find('#productDetails_detailBullets_sections1 th');
        foreach($ths as $th){
            //echo $th->text()."<br>";
            if(trim($th->text()) == 'ASIN'){
                $this->asin = $th->nextSibling('td.a-size-base')->text();
            }
        }


        $link = $document->find('#buybox-see-all-buying-choices-announce')[0];
        //echo $link->href; die;
        $allPricesPage = $link->href;

        $url = 'https://www.amazon.com/'.$allPricesPage;
        //echo $url; die;
        $document = new Document($url, true);

        $prices = $document->find('span.olpOfferPrice');
        //echo "<pre>"; print_r($prices); die;
        $ps = [];
        for($i = 0; $i < 3; $i++){
            $ps[] = $prices[$i]->text();
        }
        $this->amazon_price = implode(', ',$ps);

        $url = $this->target_link;
        $document = new Document($url, true);
        $priceTarget = $document->find('span.h-text-xl.h-text-bold')[0];
        $this->target_price = $priceTarget->text();

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}
