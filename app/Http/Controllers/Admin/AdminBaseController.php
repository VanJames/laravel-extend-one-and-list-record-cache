<?php
/**
 * Created by IntelliJ IDEA.
 * User: James(746439274@qq.com)
 * Date: 2015/5/9
 * Time: 18:56
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminBaseController extends Controller
{
    protected $_viewPre = 'admin';
    protected function logout(){
        return \Admin::logout();
    }

    
}