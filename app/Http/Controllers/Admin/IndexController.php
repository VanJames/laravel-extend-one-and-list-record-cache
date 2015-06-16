<?php
/**
 * Created by IntelliJ IDEA.
 * User: James(746439274@qq.com)
 * Date: 2015/5/9
 * Time: 19:28
 */

namespace App\Http\Controllers\Admin;

use App\AdminUser;

class IndexController extends AdminBaseController
{
    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {
        AdminUser::getInstance(true)->setData(1,'name','范旭');
        return $this->view('index');
    }
}