<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7
 * Time: 22:23
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\Product;
use app\index\model\ProDetail;
use app\index\model\ProPicture;
use \think\Db;
use \think\Request;
class Wordskill extends Controller
{

    //话术模板首页
    public function wordIndex(){

        return $this->fetch();

    }

    //产品及话术
    public function chanpinhs(){
    	if(isset($_POST['leixing'])){
    		$insert=new Product();
    		$insert->name=$_POST['leixing'];
    		$insert->addtime=date('Y-m-d H:i:s');
    		$insert->save();
    		if($insert->p_id){
    			echo 1;exit;
    		}else{
    			echo 0;exit;
    		}
    	}

    	$sql="select * from `product`";
    	$data=Db::query($sql);
    	$this->assign('list',$data);
        return $this->fetch();
    }
    //产品详情
    public function productdetail(){
    	if(isset($_GET['d_id'])){
    		$sql1="select * from `pro_detail` where d_id=".$_GET['d_id']." limit 1";
    		$data1=Db::query($sql1);
			$sql2 = 'select img_url from pro_picture where d_id ='.$_GET['d_id'];
			$data2 = Db::query($sql2);
			foreach($data2 as $key => $value){
				$arr[] = $value['img_url'];
			}	//print_r($arr);
			$this->assign('list',$data1[0]);
			$this->assign('imgArr',$arr);

			return $this->fetch();

    	}else{
			return $this->fetch();
		}

    }
    //产品子类
    public function childpro(){
    	$id=isset($_GET['p_id']) ? $_GET['p_id'] : '';

    	$sql="select d_id,name from `pro_detail` where p_id=".$id;
    	$data=Db::query($sql);
    	$this->assign('list',$data);
		$this->assign('p_id',$_GET['p_id']);
    	return $this->fetch();
    }

	//添加产品详情页面
	public function addProdetail(){

		return $this->fetch();
	}

	/*将产品的详细信息插入到数据库中*/
	public function insertProdetail(){

		$detail = new ProDetail();
		//如果是增加
		if(empty($_POST['did'])){

			$detail->name = $_POST['pro_name'];
			$detail->price = $_POST['pro_price'];
			$detail->special = $_POST['pro_special'];
			$detail->salepoint = $_POST['pro_salepoint'];
			$detail->name = $_POST['pro_name'];
			$detail->p_id = $_POST['pid'];
			$detail->imgurl1 = $_POST['pro_xc1'];
			$detail->imgurl2 = $_POST['pro_xc2'];
			$detail->addtime = time();
			$detail->save();
			if($detail->d_id){
				$pic = new ProPicture();
				$bool = $pic->where('d_id','null')->update(['d_id'=>$detail->d_id]);

				if($bool){
					echo "<script>alert('产品信息保存成功');location.href ='?s=index/wordskill/childpro&p_id='+" . $_POST['pid'] . ";</script>";
				}else{
					echo "<script>alert('产品信息保存失败');history.back();</script>";
				}

			}

		}else{

			//这是更新的操作
			$a = $detail->where('d_id',$_POST['did'])
				->update(['name'=>$_POST['pro_name'],'price'=>$_POST['pro_price'],'special'=>$_POST['pro_special'],'salepoint'=>$_POST['pro_salepoint'],'imgurl1'=>$_POST['pro_xc1'],'imgurl2'=>$_POST['pro_xc2']]);

			//查出pic表里有木有null字段
			$sql = 'SELECT COUNT(id) as count FROM pro_picture WHERE d_id is NULL;';
			$info = Db::query($sql)[0];

			if($info['count'] == 0){
				//print_r('没有上传图片');
				$bool = 0;
			}else{
				$pic = new ProPicture();
				$bool = $pic->where('d_id','null')->update(['d_id'=>$_POST['did']]);

			}
			//print_r($bool);exit();
			if($bool || $a){
					echo "<script>alert('信息更新成功');location.href ='?s=index/wordskill/childpro&p_id='+" . $_POST['pid'] . ";</script>";
				}else{
					echo "<script>alert('您并未更新内容');history.back();</script>";
				}


		}

	}

	//试试上传图片
	public function test(){
		//print_r($_POST);exit();
		$imgData = $_REQUEST['images'];
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $imgData, $result)){

			$type = $result[2];
			//目录
			$dir = dirname(dirname(dirname(dirname(__FILE__))));
			$uploadpic = $dir . "/public/static/uploads/product";
			$rand = time().rand(1000,9999);
			$new_file = $uploadpic.'/'.$rand.'.'.$type; //生成的图片
			//print_r($rand.'.'.$type);
			$str = str_replace($result[1], '', $imgData);
			$data = base64_decode($str);

			if (file_put_contents($new_file, $data)){
				//将图片路径存入数据库中
				$pic = new ProPicture();
				$pic->img_url = $rand.'.'.$type;
				$pic->addtime = time();
				$pic->save();
			}else{
				echo 123;
			}

		}
	}
	
}

