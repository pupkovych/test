<?php
/**
 * Created by PhpStorm.
 * User: ramires
 * Date: 11.03.2018
 * Time: 19:18
 */

/**
 * Class TestApiWrapper
 */
class TestApiWrapper
{
    const BASE_API_URL = "http://94.254.0.188:4000/";

    const RESPONSE_STATUS_OK = "OK";

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * TestApiWrapper constructor.
     * @param bool $debug
     */
    public function __construct($debug = false)
    {
        if ($debug) {
            $this->debug = true;
        }
    }

    /**
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param string $url
     * @param array $params
     * @return array|null
     */
    protected function get($url, $params = [])
    {
        $url = self::BASE_API_URL . $url;
        if (!empty($params)) {
            $url .= $this->arrayToGetQuery($params);
        }

        return $this->execRequest($url);
    }

    /**
     * @param string $url
     * @return array|null
     */
    private function execRequest($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
        ]);

        $response = json_decode(curl_exec($ch), true);

        if ($this->debug) {
            var_dump($response);
            die();
        }
        if (isset($response['status'])
            && $response['status'] == self::RESPONSE_STATUS_OK
            && isset($response['data'])
        ) {
            return $response['data'];
        }

        return null;
    }

    /**
     * @param array $array
     * @return string
     */
    private function arrayToGetQuery(array $array)
    {
        if (!empty($array)) {
            $requestUri = "?";
            $count = 0;
            foreach ($array as $key => $value) {
                if ($count > 0) {
                    $requestUri .= "&";
                }
                $value = urlencode($value);
                $requestUri .= $key . "=" . $value;
                $count++;
            }

            return $requestUri;
        }
        return "";
    }

    /**
     * @param array $params
     * @return array|null
     */
    public function getBooks(array $params = [])
    {
        $result = $this->get('books', $params);

        return !empty($result['books']) ? $result['books'] : [];
    }

    /**
     * @param int $authorID
     * @param array $params
     * @return array|null
     */
    public function getBooksByAuthor($authorID, array $params = [])
    {

        $result = $this->get('authors/' . (int)$authorID . '/books', $params);

        return !empty($result['books']) ? $result['books'] : [];
    }

    /**
     * @param array $params
     * @return array|null
     */
    public function getAuthors(array $params = [])
    {
        $result = $this->get('authors', $params);

        return !empty($result['authors']) ? $result['authors'] : [];
    }
}