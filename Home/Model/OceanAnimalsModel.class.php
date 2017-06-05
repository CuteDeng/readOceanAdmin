<?php

namespace Home\Model;
use Think\Model;
class OceanAnimalsModel extends Model{
	//重要
	protected $trueTableName = 'ocean_animals';
	public function addanimals($data,$timg,$dimg)
	{
        if(!$timg['error'])
        {
            $cfg=array(
                'rootPath'=>WORKING_PATH.UPLOAD_OCEANANIMALS_TIMG
                );
            $upload=new \Think\Upload($cfg);
            $info = $upload->uploadOne($timg);
            if($info){
                $data['guideUrl']=WORKING_PATH.UPLOAD_OCEANANIMALS_TIMG.$info['savepath'].$info['savename'];
            }else{
                //$this->error($upload->getError());die;
                alertToUrl(U('Data/addanimal'),'海洋生物图片上传错误');die;
            }

        }
        if(!$dimg['error'])
        {
            $cfg=array(
                'rootPath'=>WORKING_PATH.UPLOAD_OCEANANIMALS_DIMG
                );
            $upload=new \Think\Upload($cfg);
            $info = $upload->uploadOne($dimg);
            if($info){
                $data['url']=WORKING_PATH.UPLOAD_OCEANANIMALS_DIMG.$info['savepath'].$info['savename'];
            }else{
                //$this->error($upload->getError());die;
                alertToUrl(U('Data/addanimal'),'海洋生物动画上传出现错误');die;
            }
        }
    	$con['id']=getUid();
    	while (1) {
    		$flag=M('ocean_animals')->where($con)->select();
    		if($flag){
    			$con['id']=getUid();
    		}else{
    			$data['id']=$con['id'];
    			break;
    		}
    	}
    	unset($con['id']);
    	$con['name']=$data['name'];
    	$flag=M('ocean_animals')->where($con)->select();
    	if($flag)
    	{
    		alertToUrl(U('Data/addanimal'),'数据库存在该名称的海洋生物');die;
    	}
    	$data['getType']='ocean_animal_getType_byrank';
        $result=M('ocean_animals')->add($data);

        return $result;
	}
}