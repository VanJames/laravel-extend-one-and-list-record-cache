<?php
/**
 * Created by IntelliJ IDEA.
 * User: fanxu(746439274@qq.com)
 * Date: 15/6/13
 * Time: 下午11:20
 */
namespace App\Model\Core;

class Invest extends \App\DataModel\MemberDatabase{

    private static $_singleInstance = null;

    const TABLE_COUNT = 2;

    const TABLE_NAME = 'invest';

    public function __construct($mid){
        $index = $mid % self::TABLE_COUNT + 1;
        $this->table = self::TABLE_NAME . $index;
        parent::__construct();
    }

    public static function getInstance($mid){
        if( !isset(self::$_singleInstance[$mid])
            ||
            !(self::$_singleInstance[$mid] instanceof self) ){
            self::$_singleInstance[$mid] = new self($mid);
        }
    }

    public static function invest($pid,$mid,$money){
        $invest = self::getInstance($mid);
        $invest->pid=$pid;
        $invest->uid=$mid;
        $invest->invest_money=$money;
    }
}