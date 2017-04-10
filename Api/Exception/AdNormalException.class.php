<?php
namespace Api\Exception;

use Exception;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/21
 * Time: 20:40
 */
class AdNormalException extends Exception
{
    public function errorMessage()
    {
        $errorMsg = 'AdNormalException on line ' . $this->getLine() . ' in ' . $this->getFile() . ': ' . $this->getMessage();
        return $errorMsg;
    }
}