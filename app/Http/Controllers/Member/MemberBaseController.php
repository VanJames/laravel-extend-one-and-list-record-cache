<?php
/**
 * Created by IntelliJ IDEA.
 * User: James(746439274@qq.com)
 * Date: 2015/5/9
 * Time: 18:56
 */

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

class MemberBaseController extends Controller
{
    protected $_viewPre = 'member';
    public function __construct(){
        $this->middleware('auth');
    }

    protected function logout(){
        return Auth::logout();
    }

}