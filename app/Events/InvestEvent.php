<?php namespace App\Events;

use App\Model\Core\Project\Item;

class InvestEvent extends Event{

    private $_pid = 0;
    private $_mid = 0;
    private $_investMoney = 0;

    /**
     * @param $pid
     * @param $mid
     * @param $investMoney
     */
    public function __construct($pid , $mid , $investMoney){
        $this->_pid=$pid;
        $this->_mid=$mid;
        $this->_investMoney=$investMoney;
        parent::__construct();
    }

    public static function getInstance( $pid , $mid , $investMoney ){
        return new self( $pid , $mid , $investMoney );
    }

	public function handle(){

    }

    protected function checkParam(){
    }

}
