<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\AdminBaseController;

class LoginController extends AdminBaseController
{

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    /**
     * 后台登录
     */
    public function getIndex()
    {
        if( !\Admin::guest() ){
            return redirect('/a');
        }
        return $this->view('auth.login');
    }

    /**
     * 登出
     */
    public function getLogout()
    {
        return $this->logout();
    }

    /**
     * 登入
     */
    public function postDo()
    {
        if(\Admin::guest()){
            return redirect('admin_login')->withErrors(['登录失败,账号与密码不符合!']);
        }
        return redirect('/a');
    }

}
