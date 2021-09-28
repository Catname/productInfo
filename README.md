<h1 align="center"> productInfo </h1>

<p align="center"> Get product information by GS1 code.</p>

### Author: ZhangHQ

## Installing

```shell
$ composer require catname/productinfo
```

## Usage

```php
use Catname\ProductInfo\ProductInfo;

class example
{
    public function aExample(Request $request)
    {
        $productInfo = new ProductInfo();
        return $productInfo->getProductInfo($request->code);
    }
    
    /**
    * array (size=4)
        'code' => string '06907992512570' (length=14)
        'supplier' => string '内蒙古伊利实业集团股份有限公司' (length=45)
        'name' => string '安慕希希腊风味酸奶205克' (length=33)
        'specs' => string '205克' (length=6)
    */
}
```


## License

MIT