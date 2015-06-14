<?php
/**
 * Created by IntelliJ IDEA.
 * User: fanxu(746439274@qq.com)
 * Date: 15/6/13
 * Time: 下午11:20
 */
namespace App\Model\Member;

class Account extends \App\DataModel\MemberDatabase{

    protected $table='account';
    private static $_singleInstance = null;
    public static function getInstance($mid){
        if( !isset(self::$_singleInstance[$mid])
            ||
            !(self::$_singleInstance[$mid] instanceof self) ){
            self::$_singleInstance[$mid] = new self();
        }
    }

    public function getCacheKey(){

    }
}