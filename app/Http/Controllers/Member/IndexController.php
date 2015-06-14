<?php
/**
 * Created by IntelliJ IDEA.
 * User: James(746439274@qq.com)
 * Date: 2015/5/9
 * Time: 19:28
 */

namespace App\Http\Controllers\Member;

use App\User;

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
}