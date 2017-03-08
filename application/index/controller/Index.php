<?php
// I am not sure why this works but it fixes the problem. 
// 虽然我不知道为什么这样管用，但它却是修复了问题
// Move on and call me an idiot later.
// 你我都知道这代码很烂
// 佛祖保佑代码永无BUG
// @yiwlny   代号：小海     名言：你的指尖有改变世界的力量

/**                                                                          
 *          .,:,,,                                        .::,,,::.          
 *        .::::,,;;,                                  .,;;:,,....:i:         
 *        :i,.::::,;i:.      ....,,:::::::::,....   .;i:,.  ......;i.        
 *        :;..:::;::::i;,,:::;:,,,,,,,,,,..,.,,:::iri:. .,:irsr:,.;i.        
 *        ;;..,::::;;;;ri,,,.                    ..,,:;s1s1ssrr;,.;r,        
 *        :;. ,::;ii;:,     . ...................     .;iirri;;;,,;i,        
 *        ,i. .;ri:.   ... ............................  .,,:;:,,,;i:        
 *        :s,.;r:... ....................................... .::;::s;        
 *        ,1r::. .............,,,.,,:,,........................,;iir;        
 *        ,s;...........     ..::.,;:,,.          ...............,;1s        
 *       :i,..,.              .,:,,::,.          .......... .......;1,       
 *      ir,....:rrssr;:,       ,,.,::.     .r5S9989398G95hr;. ....,.:s,      
 *     ;r,..,s9855513XHAG3i   .,,,,,,,.  ,S931,.,,.;s;s&BHHA8s.,..,..:r:     
 *    :r;..rGGh,  :SAG;;G@BS:.,,,,,,,,,.r83:      hHH1sXMBHHHM3..,,,,.ir.    
 *   ,si,.1GS,   sBMAAX&MBMB5,,,,,,:,,.:&8       3@HXHBMBHBBH#X,.,,,,,,rr    
 *   ;1:,,SH:   .A@&&B#&8H#BS,,,,,,,,,.,5XS,     3@MHABM&59M#As..,,,,:,is,   
 *  .rr,,,;9&1   hBHHBB&8AMGr,,,,,,,,,,,:h&&9s;   r9&BMHBHMB9:  . .,,,,;ri.  
 *  :1:....:5&XSi;r8BMBHHA9r:,......,,,,:ii19GG88899XHHH&GSr.      ...,:rs.  
 *  ;s.     .:sS8G8GG889hi.        ....,,:;:,.:irssrriii:,.        ...,,i1,  
 *  ;1,         ..,....,,isssi;,        .,,.                      ....,.i1,  
 *  ;h:               i9HHBMBBHAX9:         .                     ...,,,rs,  
 *  ,1i..            :A#MBBBBMHB##s                             ....,,,;si.  
 *  .r1,..        ,..;3BMBBBHBB#Bh.     ..                    ....,,,,,i1;   
 *   :h;..       .,..;,1XBMMMMBXs,.,, .. :: ,.               ....,,,,,,ss.   
 *    ih: ..    .;;;, ;;:s58A3i,..    ,. ,.:,,.             ...,,,,,:,s1,    
 *    .s1,....   .,;sh,  ,iSAXs;.    ,.  ,,.i85            ...,,,,,,:i1;     
 *     .rh: ...     rXG9XBBM#M#MHAX3hss13&&HHXr         .....,,,,,,,ih;      
 *      .s5: .....    i598X&&A&AAAAAA&XG851r:       ........,,,,:,,sh;       
 *      . ihr, ...  .         ..                    ........,,,,,;11:.       
 *         ,s1i. ...  ..,,,..,,,.,,.,,.,..       ........,,.,,.;s5i.         
 *          .:s1r,......................       ..............;shs,           
 *          . .:shr:.  ....                 ..............,ishs.             
 *              .,issr;,... ...........................,is1s;.               
 *                 .,is1si;:,....................,:;ir1sr;,                  
 *                    ..:isssssrrii;::::::;;iirsssssr;:..                    
 *                         .,::iiirsssssssssrri;;:.                      
 */                         



namespace app\index\controller;

use think\Controller;
use app\index\model\User;
use think\Session;
class Index extends \think\Controller
{	

	protected $beforeActionList = [
       'gosession' =>  ['except'=>'login,login_all'],    //tp前置方法，不管执行那个方法，都要先执行gosession ， 除了login,login_all方法
    ];

    //定义前置控制器
    public function gosession()
    {   
        $id=Session::get('id');
    	if(!$id)
    	{
    		$this->error('请先登录','login');
    	}
    }


    //用户管理首页， 登录成功后的页面
    public function index()
    {
        $db = db('user');
    	$data = $db->select();
		return $this->fetch('index',['data'=>$data]);
    }

    //退出登录
    public function login_out()
    {
    	session::clear();
        $this->success('退出成功','login');
    }

    //登录页面
    public function login()
    {

    	return $this->fetch('login');

    }

    //用户登录方法
    public function login_all()
    {
    	$db = db('user');
    	$name = input('post.name');
     	$password = input('post.password');
        // 查询数据
		$list = $db->where(['name'=>$name,'password'=>$password])->find();

        //如果存在就存入session，并且跳转首页
		if($list)
		{	

			Session::set('name',$name);
			Session::set('id',$list['id']);

			$db->where(['name'=>$name,'password'=>$password])->update(['logintime'=>date("Y-m-d H:i:s")]);
			$this->redirect('index');
		}else
		{
			$this->error('登录失败','login');
		}
    }




    //用户添加页面
    public function add()
    {
    	return $this->fetch('add');
    }


    //用户添加方法
    public function add_all()
    {
        $name = input('post.name');
        $password = input('post.password');
        $db = db('user');
        //添加用户
        $list = $db->insert(['name' => $name, 'password' => $password]);
        //判断添加用户是否成功
        if($list){
            $this->success('添加成功!','index');
        }else{
            $this->error('添加错误!','index');
        }
    }

    //用户修改页面
    public function update($id)
    {     
        $db = db('user');
        $data = $db->where(['id'=>$id])->find();
    	return $this->fetch('update',['data'=>$data]);
    }

    //用户修改方法
    public function update_all($id)
    {
        $db = db('user');
        $name = input('name');
        $password = input('password');
        $upd = $db->where(['id'=>$id])->update(['name'=>$name,'password'=>$password]);
        if($upd){
            echo '修改成功';
            $this->redirect('index');
        }else{
            echo '修改失败';
        }
    }

    //删除用户方法
    public function delete($id)
    {
        $db = db('user');
        $del=$db->where(['id' => $id])->delete();
        $this->redirect('index');
    }

}