<?php namespace App\Events;

abstract class Event {

    public function __construct(){
        $this->checkParam();
    }

    /**
     * 校验参数
     * @return mixed
     */
    abstract protected function checkParam();
}
