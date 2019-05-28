<?php
/**
*	管理首页-微信管理
*	主页：http://www.rockoa.com/
*	软件：信呼OA云平台
*	作者：雨中磐石(rainrock)
*	时间：2018-06-18
*/

namespace App\RainRock\Chajian\Unitage;

use App\Model\Base\UseraModel;
use App\Model\Base\WxqyagentModel;

class ChajianUnitage_wxqy extends ChajianUnitage
{

	
	public function cogForm($request)
	{
		$copt = $this->getNei('option');
		
		return [
			'wxqycorpid' => $copt->getval('weixinqy_corpid'),
			'wxqysecret' => $copt->getval('weixinqy_secret'),
			'wxqydepid' => $copt->getval('weixinqy_deptid'),
			'wxqytodo' => $copt->getval('weixinqy_todo',0,'1'),
			'wxqyhuitoken' => $copt->getval('weixinqy_huitoken'),
			'wxqyaeskey' => $copt->getval('weixinqy_aeskey'),
			'wxqyhuiurl' => route('apiwe',['wxqy',$this->companyinfo->num]),
		];
	}
	
	public function deptForm($request)
	{
		$depta		= $wdepta = array();
		$deptdata 	= $this->getNei('dept')->getDeptArr($this->companyid);
		
		foreach($deptdata as $k=>$rs)$depta[$rs->id] = $rs;
		$wdeptdata 	= $this->getNei('Wxqy:dept')->getDeptArr($this->companyid);
		
		foreach($wdeptdata as $k=>$rs){
			$wdepta[$rs->id] = $rs;
			$iscz	= true;
			if($rs->parentid>0)$iscz = isset($depta[$rs->id]);
			$wdeptdata[$k]->iscz = $iscz;
		}
		
		foreach($deptdata as $k=>$rs){
			$istb = 0;
			$id   = $rs->id;
			
			if(isset($wdepta[$id])){
				$istb = 1; //已同步
				if($rs->name!=$wdepta[$id]->name)$istb = 3;
			}else{
				$istb = 2; //需更新
			}
			$deptdata[$k]->istb = $istb;
		}
		return [
			'deptdata' 	=> $deptdata,
			'wdeptdata' => $wdeptdata,
		];
	}
	
	public function userForm($request)
	{
		$obj 	= UseraModel::select();
		$did 	= (int)$request->get('did',0);
		$obj->where('cid' ,$this->companyid);
		if($did>0)$obj->where('deptid', $did);
		
		$key 	= trim($request->get('keyword'));
		$zt 	= trim($request->get('souzt'));
		if(!isempt($key)){
			$obj->where(function($query)use($key){
				$query->where('name','like',"%$key%");
				$query->oRwhere('user',$key);
				$query->oRwhere('mobile','like',"%$key%");
				$query->oRwhere('deptname','like',"%$key%");
				$query->oRwhere('superman','like',"%$key%");
			});
		}
		
		$where = '';
		
		
		if($zt=='0' || $zt=='1' || $zt=='2')$obj->where('status', $zt);
		
		$qz = \DB::getTablePrefix();
		
		//已激活的
		if($zt=='3'){
			$where="`user` in(select `userid` from `".$qz."wxqyuser` where `status`=1)";
		}
		//未激活的
		if($zt=='4'){
			$where="`user` in(select `userid` from `".$qz."wxqyuser` where `status`=4)";
		}
		//未创建
		if($zt=='5'){
			$where="`user` not in(select `userid` from `".$qz."wxqyuser`)";
		}
		if($where!='')$obj->whereRaw($where);

		$total 	= $obj->count();
		$data 	= $obj->orderBy('sort','desc')->simplePaginate($this->limit)->getCollection();
		
		
		$ulist  = $wxuser = array();
		foreach($data as $k=>$rs){
			if(!isempt($rs->user))$ulist[] = $rs->user;
		}
		
		$obj	= $this->getNei('Wxqy:user');
		
		if($ulist)$wxuser = $obj->getuserarr($ulist);
		
		foreach($data as $k=>$rs){
			$wxstatus = 0; //未创建
			$user = $rs->user;
			$jihuo= 0;
			$isgc	= 0;
			$gxls	= '';
			if(!isempt($user)){
				if(isset($wxuser[$user])){
					$wxrs = $wxuser[$user];
					$wxstatus = 1; //已创建
					$jihuo = $wxrs->status;
					
					$gxarr	= $obj->isgeng($wxrs,$rs);
					$isgc	= $gxarr[0];
					$gxls	= $gxarr[1];
				}
			}
			
			
			$data[$k]->isgc = $isgc;
			$data[$k]->gxls = $gxls;
			$data[$k]->wxstatus = $wxstatus;
			$data[$k]->jihuo = $jihuo;
		}
		
		
		$bucz	= $obj->getnoinsys();
		
		return [
			'deptdata' 	=> c('dept')->getDeptArr($this->companyid),
			'did'		=> $did,
			'data'		=> $data,
			'total'		=> $total,
			'bucz'		=> join(',', $bucz),
			'souzt'		=> $zt,
			'pager'		=> [
				'did' 	=> $did,
			]
		];
	}
	
	public function agentForm($request)
	{
		$data = WxqyagentModel::where('cid',$this->companyid)->orderBy('sort')->get();
		return [
			'data' 	=> $data,
			'mtable' => c('rockjm')->encrypt('wxqyagent')
		];
	}
}