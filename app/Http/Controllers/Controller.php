<?php namespace App\Http\Controllers;

use App\Exceptions\AppException;
use App\Model\ModelContainer;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

	protected $checkFields = [];
	protected $checkRules = [];
	public function __construct(){
		if(!empty($this->checkFields)){
			//$this->validate($this->checkRules);
		}
	}

	/**
	 * @var string
	 */
	protected $_viewPre='';

	/**
	 * @param null $view
	 * @param array $data
	 * @param array $mergeData
	 * @return \Illuminate\View\View
	 */
	protected function view($view = null, $data = array(), $mergeData = array()){
		$this->_viewPre && $this->_viewPre .= '.';
		return view($this->_viewPre.$view, $data , $mergeData );
	}

	/**
	 * @param $data
	 * @return mixed
	 * @throws AppException
	 */
	protected function ajaxReturn($data){
		if(!is_array($data)){
			throw new AppException( AppException::ERROR_CODE_TYPE );
		}
		return \Response::json([
			'error' => 0,
			'msg' => '',
			'data' => $data
		]);
	}

	public function __destruct(){
		ModelContainer::save();
	}
}
