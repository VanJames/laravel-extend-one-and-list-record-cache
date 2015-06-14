<?php
/**
 * Created by IntelliJ IDEA.
 * User: fanxu(746439274@qq.com)
 * Date: 15/6/13
 * Time: 下午11:20
 */
namespace App\Model\Core\Project;

class Item extends \App\DataModel\MemberDatabase{

    const TABLE_NAME = 'project';

    const CACHE_KEY = '_projectItem';

    public function __construct(){
        $this->table = self::TABLE_NAME ;
        parent::__construct();
    }

    public static function getInstance($pid){

    }

    public static function create($pid,$mid,$money){
    }

    public static function has($pid){

    }
}