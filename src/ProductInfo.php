<?php
/**
 * @author: ZhangHQ
 * @email : tomcath@foxmail.com
 */
namespace Catname\ProductInfo;

use GuzzleHttp\Client;
use voku\helper\HtmlDomParser;

class ProductInfo
{
    protected $client;
    protected $url;
    protected $getCookieURL = 'http://search.anccnet.com/writeSession.aspx?responseResult=check_ok';
    protected $cookie;
    protected $headers;
    public $message;

    public function __construct()
    {
        $this->client = new Client();
        $this->headers = [
            'Accept' => '*/*',
            'User-Agent' => $this->getUserAgent(),
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Language' => 'zh-CN,zh;q=0.9'
        ];
    }

    /**
     * 传入商品码获取商品名称，规格，厂家，失败返回false，原因见$this->message
     * @param $code /商品码
     * @return array|false
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author: ZhangHQ
     * @email : tomcath@foxmail.com
     */
    public function getProductInfo($code)
    {
        $this->url = 'http://search.anccnet.com/searchResult2.aspx?keyword=' . $code;
        $this->headers['Cookie'] = $this->getCookie();// 必须在url赋值后调用

        if ($this->headers['Cookie'] == false) {
            $this->message = '操作失败：获取Cookie失败';
            return false;
        }

        $response = $this->client->request('GET', $this->url, [
            'headers' => $this->headers
        ]);

        $html = HtmlDomParser::str_get_html($response->getBody()->getContents());

        // 构造结果
        $productInfo = [
            'code' => $html->find('#results > li > div > dl.p-info > dd:nth-child(2) > a', 0)->plaintext,
            'supplier' => $html->find('#repList_ctl00_firmLink',0)->plaintext,
            'name' => $html->find('#results > li > div > dl.p-info > dd:nth-child(6)', 0)->plaintext,
            'specs' => $html->find('#results > li > div > dl.p-info > dd:nth-child(8)', 0)->plaintext,
        ];

        //返回结果
        return $productInfo;

    }

    /**
     * @return false|mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author: ZhangHQ
     * @email : tomcath@foxmail.com
     */
    protected function getCookie()
    {
        $this->headers['Referer'] = $this->url;
        unset($this->headers['Cookie']);
        $response = $this->client->request('GET', $this->getCookieURL, [
            'headers' => $this->headers
        ]);
        if ($response->getStatusCode() == 200) {
            $cookie = $response->getHeader('Set-Cookie');
            return explode(';', $cookie[0])[0];
        } else {
            return false;
        }

    }

    /**
     * @return string
     * @author: ZhangHQ
     * @email : tomcath@foxmail.com
     */
    protected function getUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36 Edg/92.0.902.84',
            'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36 SE 2.X MetaSr 1.0',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
        ];
        return $userAgents[array_rand($userAgents)];
    }


}