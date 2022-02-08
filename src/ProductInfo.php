<?php
/**
 * @author: ZhangHQ
 * @email : tomcath@foxmail.com
 */
namespace Catname\ProductInfo;

use GuzzleHttp\Client;

class ProductInfo
{
    protected $client;
    protected $url;
    protected $headers;
    public $message;

    public function __construct()
    {
        $this->client = new Client();
        $this->headers = [
            'Accept'             => 'application/json, text/plain, */*',
            'Accept-Encoding'    => 'gzip, deflate, br',
            'Accept-Language'    => 'zh-CN,zh;q=0.9',
            'Connection'         => 'keep-alive',
            'Host'               => 'bff.gds.org.cn',
            'Origin'             => 'https://www.gds.org.cn',
            'Referer'            => 'https://www.gds.org.cn/',
            'sec-ch-ua'          => '" Not A;Brand";v="99", "Chromium";v="98", "Google Chrome";v="98"',
            'sec-ch-ua-mobile'   => '?0',
            'sec-ch-ua-platform' => '"Windows"',
            'Sec-Fetch-Dest'     => 'empty',
            'Sec-Fetch-Mode'     => 'cors',
            'Sec-Fetch-Site'     => 'same-site',
            'User-Agent'         => $this->getUserAgent(),

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
        $this->url = 'https://bff.gds.org.cn/gds/searching-api/ProductService/ProductListByGTIN';

        $response = $this->client->request('GET', $this->url, [
            'headers' => $this->headers,
            'query'   => [
                'PageSize'   => 30,
                'PageIndex'  => 1,
                'SearchItem' => $code,
            ]
        ]);

        $result = json_decode($response->getBody()->getContents());
        // 构造结果
        if ($result->Msg == 'Success') {
            $item = $result->Data->Items[0];
            $productInfo = [
                'code'     => $item->gtin,
                'supplier' => $item->firm_name,
                'name'     => $item->description,
                'specs'    => $item->specification,
            ];
            //返回结果
            return $productInfo;
        } else {
            $this->message = $result->Msg;
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
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.82 Safari/537.36',
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