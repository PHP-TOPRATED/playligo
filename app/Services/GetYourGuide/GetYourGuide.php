<?php

namespace App\Services\GetYourGuide;


use App\Services\GetYourGuide\Entities\Tour;
use Illuminate\Support\Collection;

class GetYourGuide
{
        /**
     * @var string
     */
    const API_VERSION = 1; // from the config file;
    /**
     * @var array
     */
    public $APIs = array(
        'tours.list'     => 'https://api.getyourguide.com/%s/tours',
        'tours.item'     => 'https://api.getyourguide.com/%s/tours',
        'locations.list' => 'https://api.getyourguide.com/%s/locations',
        'locations.item' => 'https://api.getyourguide.com/%s/locations',
    );
    /**
     * @var array
     */
    public $page_info = array();
/**
     * @var string
     */
    protected $api_key;
    protected $cnt_language;
    protected $currency;

    /**
     * Constructor
     *
     * @param $key
     * @throws \Exception
     */
    public function __construct($key)
    {
        $cnt_language = config('getyourguide.cnt_language');
        $currency = config('getyourguide.currency');

        if (is_string($key) && !empty($key)) {
            $this->api_key = $key;
        } else {
            throw new \Exception('GetYourGuide API Key is Required');
        }
        if (is_string($cnt_language) && !empty($cnt_language)) {
            $this->cnt_language = $cnt_language;
        } else {
            $this->cnt_language = 'en';
        }
        if (is_string($currency) && !empty($currency)) {
            $this->currency = $currency;
        } else {
            $this->currency = 'USD';
        }
    }

    /**
     * Returns lists of tours
     *
     * @param array $params
     * @return array
     */
    public function getTours($params = array())
    {
        $API_URL = $this->getApi('tours.list');

        $apiData = $this->api_get($API_URL, $params);

        return array(
            'results' => $this->decodeList($apiData),
            'info' => $this->page_info,
        );
    }

    /**
     * Returns url of specified API Method
     *
     * @param $name
     * @return array
     */
    public function getApi($name)
    {
        return sprintf($this->APIs[$name], $this::API_VERSION);
    }

    /**
     * Using CURL to issue a GET request
     *
     * @param $url
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function api_get($url, $params)
    {
        $params = array_merge($params, [
            'cnt_language' => $this->cnt_language,
            'currency'     => $this->currency
        ]);
        //boilerplates for CURL
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, $url . (strpos($url, '?') === false ? '?' : '') . http_build_query($params));

        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, [
            'x-access-token: ' . $this->api_key,
            'Accept: application/json',
        ]);


        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        $tuData = curl_exec($tuCurl);
        if (curl_errno($tuCurl)) {
            throw new \Exception('Curl Error : ' . curl_error($tuCurl));
        }

        return $tuData;
    }

    /**
     * Decode the response from youtube, extract the list of resource objects
     *
     * @param  string $apiData response string from youtube
     * @throws \Exception
     * @return bool|Collection
     */
    public function decodeList($apiData)
    {
        $resObj = json_decode($apiData);
        $metadata = isset($resObj->_metadata) ? $resObj->_metadata : $resObj;
        if ($metadata->status === "ERROR") {
            $msg = null;
            foreach ($metadata->errors as $error) {
                $msg .= $error->errorMessage . PHP_EOL;
            }

            throw new \Exception($msg);
        } else {
            $collection = new Collection();
            $data = isset($resObj->data) ? $resObj->data : $resObj;

            if(isset($data->tours)) {
                foreach ($data->tours as $tour) {
                    $collection->push(
                        new Tour(json_decode(json_encode($tour), true))
                    );
                }
            }

            $this->page_info = array(
                'totalCount' => $metadata->totalCount,
                'limit'      => $metadata->limit,
                'offset'     => $metadata->offset
            );

            if (count($collection) == 0) {
                return false;
            } else {
                return $collection;
            }
        }
    }

}