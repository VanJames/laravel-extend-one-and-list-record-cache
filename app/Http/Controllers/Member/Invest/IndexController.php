<?php
/**
 * Created by IntelliJ IDEA.
 * User: James(746439274@qq.com)
 * Date: 2015/5/9
 * Time: 19:28
 */

namespace App\Http\Controllers\Member\Invest;

use App\Http\Controllers\Member\MemberBaseController;
use App\Model\Core\Project\Item;

class IndexController extends MemberBaseController
{
    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {
        return $this->view('index');
    }

    /**
     * 投标
     * @return
     */
    public function postInvest()
    {
        $this->validate(
            [
                'mid'=>'required|integer|min:1',
                'pid'=>'required|integer|min:1',
                'invest_money'=>'required|min:100',
            ]
        );
        $mid = \InputData::getPositiveInteger('mid');
        $pid = \InputData::getPositiveInteger('pid');
        $invest_money = \InputData::getPositiveInteger('invest_money');

    }

}