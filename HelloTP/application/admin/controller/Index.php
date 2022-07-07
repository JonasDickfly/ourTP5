<?php
namespace app\admin\controller;//定义以下类所在的命名空间
use think\View;//引用命名空间
use think\Controller;
use think\Session;
use think\Db;
use app\common\model\Admin;
class Index extends Controller//定义一个类（控制器）,继承TP5的Controller类
{    
    public function index()
    {   
        $adminName=Session::get('adminName');
        if($adminName==""){//未登录
         return view('login');//返回登录视图
        }
        else{
         return view();//返回首页视图    
        }
        return view();//返回首页视图        
    }
    public function register()    
    {      //return alert_error('您好，欢迎光顾来到博客园'); 
        return view();//直接返回视图    
    }
    public function registerCheck()    
    {        $admin = new Admin($_POST);
        if($admin->uname=="" || $admin->upwd==""){
            //return alert_error('错误，用户名/密码不能为空！');
            return $this->error('错误，用户名/密码不能为空！');
        }
        if($admin->upwd!=input("upwd1")){//使用input函数可以获取输入框upwd1的值
            //return alert_error('错误，两次输入密码不相同！');
            return $this->error('错误，两次输入密码不相同！');
        }        
        try{
            // 过滤post数组中的非数据表字段数据
            $admin->allowField(true)->save();
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
    public function loginCheck($adminName='',$adminPwd='')    
    {        
        $code=input('yzm');
        $captcha = new \think\captcha\Captcha();
        $result=$captcha->check($code);
        if($result===false){
            echo '验证码错误';exit;
        }

        //根据名字和密码查询记录        
        $admin = Admin::get(['adminName' => $adminName, 'adminPwd' => $adminPwd]);        
        if($admin){            
            //登录成功，保存用户名到session            
            Session::set('adminName',$adminName);            
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
        Session::delete('adminName');        
        // 清除session（当前作用域）       
        //Session::clear();            
        return $this->redirect('index');    
    }
    public function show_yzm(){
        $captcha = new \think\captcha\Captcha();
        $captcha->imageW=121;
        $captcha->imageH = 32;  //图片高
        $captcha->fontSize =14;  //字体大小
        $captcha->length   = 4;  //字符数
        $captcha->fontttf = '5.ttf';  //字体
        $captcha->expire = 30;  //有效期
        $captcha->useNoise = false;  //不添加杂点
        return $captcha->entry();
    }

     //上传图片
     public function uploadPic(){
    	return view();
    }
    public function upload_image(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');    
        // 移动到框架应用根目录/public/upload/ 目录下
        if($file){
            //$info = $file->move(ROOT_PATH . 'public' . DS . 'upload');//按日期生成子目录，文件重命名
            //$info = $file->move(ROOT_PATH . 'public' . DS . 'upload','');//存放upload下用原文件名
            //只上传图片存放upload下文件重命名
            $info = $file->validate(['ext'=>'jpg,png,gif,bmp,jpeg'])->rule('uniqid')->move(ROOT_PATH . 'public/static' . DS . 'upload');
            if($info){
                $this->success("上传成功，保存的文件名为：".$info->getSaveName());
                // 成功上传后 获取上传信息
                // 输出 jpg
                // echo $info->getExtension();
                // // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                // echo $info->getSaveName();
                // // 输出 42a79759f284b767dfcb2a0197904287.jpg
                // echo $info->getFilename(); 
            }else{
                $this->error("上传失败：".$file->getError());
                // 上传失败获取错误信息
                //echo $file->getError();
            }
        }
	}
    //删除图片
    public function deletePic(){
    	$dir=dirname(dirname(dirname(dirname(__FILE__))))."/public/static/upload";		
		$file=scandir($dir);
		//var_dump($file);
		$this->assign('images',$file);		
    	return view();
    }
    public function delete_image($pic=''){
    	$parentPath=dirname(dirname(dirname(dirname(__FILE__))))."/public/static/upload/"; //当前文件所在目录的上一级目录
		$filename=$parentPath . $pic;
		if ($pic!="" && file_exists($filename))
		{ 
	      unlink($filename);
	      $this->success("删除成功");	 
	    } 
		else
		{
			$this->error("删除失败，文件不存在。");		
		}
    }


}
