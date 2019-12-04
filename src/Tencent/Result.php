<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/12/3 0003
 * Time: 11:44
 */

namespace EasySwoole\Oss\Tencent;


use EasySwoole\HttpClient\Bean\Response;

class Result
{
    public $Location = 'service.cos.myqcloud.com/';


    function __construct(Response $response, array $operationsResult)
    {
        $this->handelData($response, $operationsResult);
    }


    function handelData(Response $response, array $operationsResult)
    {
        $body = $response->getBody();
//        var_dump($response, $operationsResult);

        //现在看上去只有object
//        if ($operationsResult['type']=='object'){ }
        if ($operationsResult['additionalProperties']) {
            $this->addProperties($response, $operationsResult['properties']);
        }
    }

    function addProperties(Response $response, $properties)
    {
        $xmlBody = simplexml_load_string($response->getBody());
        $jsonData = json_encode($xmlBody);
        $body = json_decode($jsonData, true);
        foreach ($properties as $key => $property) {
            $propertyValue = [];
            switch ($property['location']) {
                case "xml":
                    $propertyValue = $body[$key];
                    break;
                case "header":
                    $propertyValue = $response->getHeaders()[$property['sentAs']];
                    break;
            }
            if ($property['type'] == 'array') {
                $this->$key[] = $propertyValue;
            } else {
                $this->$key = $propertyValue;
            }
        }
    }


}