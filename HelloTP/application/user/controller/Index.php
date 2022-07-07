<?php
namespace app\user\controller;//定义以下类所在的命名空间
use think\View;//引用命名空间
use think\Controller;
use think\Session;
use think\Db;
use app\common\model\Users;
class Index extends Controller//定义一个类（控制器）,继承TP5的Controller类
{    
    public function index()
    {      //return "aaa";  
        return view();//直接返回视图    
    } 
    public function register()    
    {      //return alert_error('您好，欢迎光顾来到博客园'); 
        return view();//直接返回视图    
    }
    public function registerCheck()    
    {        $user = new Users($_POST);
        if($user->uname=="" || $user->upwd==""){
            //return alert_error('错误，用户名/密码不能为空！');
            return $this->error('错误，用户名/密码不能为空！');
        }
        if($user->upwd!=input("upwd1")){//使用input函数可以获取输入框upwd1的值
            //return alert_error('错误，两次输入密码不相同！');
            return $this->error('错误，两次输入密码不相同！');
        }        
        try{
            // 过滤post数组中的非数据表字段数据
            $user->allowField(true)->save();
        }
        catch(\Exception $e){  //捕获异常
            $this->error($e->getMessage());
        }       //return view('index');
        //return $this->redirect('index');
        $this->success('注册成功！', 'index');    
    }    
    public function login()    
    {       
        return view();//直接返回视图    
    }    
    //接收两个参数，注意参数名与html文件中的控件名相同    
    public function loginCheck($username='',$userpwd='')    
    {        
        //根据名字和密码查询记录        
        $user = Users::get(['uname' => $username, 'upwd' => $userpwd]);        
        if($user){            
            //登录成功，保存用户名到session            
            Session::set('username',$username);            
            //重定向             
            return $this->redirect('index');        
        }
        else
        {            
            return $this->error('登录失败');        
        }    
    }
    //退出登录    
    public function logout()    
    {
        // 删除（当前作用域）        
        Session::delete('username');        
        // 清除session（当前作用域）       
        //Session::clear();            
        return $this->redirect('index');    
    }

}
