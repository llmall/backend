<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Helpers\Code;
use App\Service\Code\EmailService;
use App\Service\Code\PhoneService;

class Helper
{

    //返回成功
    public function success($data)
    {
        return $this->result(Code::SUCCESS, Code::getMessage(Code::SUCCESS), $data);
    }

    //返回错误
    public function error($code = 422, $message = '', $data = [])
    {
        if (empty($message)) {
            return $this->result($code, Code::getMessage($code), $data);
        } else {
            return $this->result($code, $message, $data);
        }
    }

    public function result($code, $message, $data)
    {
        return ['code' => $code, 'message' => $message, 'data' => $data];
    }

    public function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 生成随机数
     * @param number $length
     * @return number
     */
    public function generateNumber($length = 6)
    {
        return rand(pow(10, ($length - 1)), pow(10, $length) - 1);
    }

    /**
     * 生成随机字符串
     * @param number $length
     * @param string $chars
     * @return string
     */
    public function generateString($length = 6, $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz')
    {
        $chars = str_split($chars);

        $chars = array_map(function ($i) use ($chars) {
            return $chars[$i];
        }, array_rand($chars, $length));

        return implode($chars);
    }

    /**
     * xml to array 转换
     * @param type $xml
     * @return type
     */
    public function xml2array($xml)
    {
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 获取ip
     * @return mixed
     */
    public static function getip($request)
    {
        $res = $request->getServerParams();
        if(isset($res['http_client_ip'])){
            return $res['http_client_ip'];
        }elseif(isset($res['http_x_real_ip'])){
            return $res['http_x_real_ip'];
        }elseif(isset($res['http_x_forwarded_for'])){
            //部分CDN会获取多层代理IP，所以转成数组取第一个值
            $arr = explode(',',$res['http_x_forwarded_for']);
            return $arr[0];
        }else{
            return $res['remote_addr'];
        }

    }

    /**
     * 获取字符串类型
     * @param $account
     * @param $is_class
     * @return string
     */
    public static function getAccountType($account, $is_class = false): string
    {
        if (preg_match('/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i', $account)) {
            $type = $is_class ? EmailService::class : 'email';
        } elseif (preg_match('/^1[34578]\d{9}$/', $account)) {
            $type = $is_class ? PhoneService::class : 'phone';
        } else {
            $type = 'username';
        }
        return $type;
    }

}