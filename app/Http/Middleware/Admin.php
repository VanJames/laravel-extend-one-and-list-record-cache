<?php
/**
 * 后台检查登录中间件
 * Created by IntelliJ IDEA.
 * User: James(746439274@qq.com)
 * Date: 2015/5/9
 * Time: 13:01
 */

namespace App\Http\Middleware;

use Closure;

use App\AdminUser;

class Admin
{
    /**
     * 当前的管理员信息
     * @var int
     */
    private $_userInfo = null;

    public function __construct()
    {
        $this->_checkLogin();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->_userInfo) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect('admin_login');
            }
        }

        return $next($request);
    }

    /**
     * 获取管理员id
     * @return int
     */
    public function user()
    {
        return $this->_userInfo;
    }

    /**
     * 是否未登录
     * @return bool
     */
    public function guest()
    {
        return empty($this->_userInfo);
    }

    /**
     * 注销登录
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        $this->_userInfo = null;
        \Session::forget('admin_user_id');
        \Cookie::forget('admin_user_id');
        return redirect('admin_login');
    }

    /**
     * 检查登录
     */
    private function _checkLogin()
    {
        if (\Session::has('admin_user_id')) {
            $this->_userInfo = AdminUser::getInstance()->getData((integer)\Session::get('admin_user_id'));
            $this->_userInfo = json_decode(json_encode($this->_userInfo));
        } elseif (\Cookie::has('admin_user_id')) {
            $this->_userInfo = AdminUser::getInstance()->getData((integer)\Cookie::get('admin_user_id'));
            $this->_userInfo = json_decode(json_encode($this->_userInfo));
        } else {
            //登录操作
            if (\Input::has('email') && \Input::has('password')) {
                $this->_valid();
                $this->_userInfo = AdminUser::get(['email'=>\Input::get('email')]);
                if( $this->_userInfo && \Hash::check(\Input::get('password'),$this->_userInfo->password) ){
                    \Session::set('admin_user_id', $this->_userInfo->id);
                    //记住我
                    if (\Input::has('remember') && \Input::get('remember')) {
                        cookie('admin_user_id', $this->_userInfo->id);
                    }
                }
            }
        }
    }

    //校验数据
    private function _valid()
    {

    }

}
