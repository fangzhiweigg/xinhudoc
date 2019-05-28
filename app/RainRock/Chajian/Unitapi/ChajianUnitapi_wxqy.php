<?php
/**
*	企业微信相关
*	主页：http://www.rockoa.com/
*	软件：信呼OA云平台
*	作者：雨中磐石(rainrock)
*	时间：2018-06-18
*/

namespace App\RainRock\Chajian\Unitapi;

use App\Model\Base\WxqyagentModel;
use App\Model\Base\AgenhModel;

class ChajianUnitapi_wxqy extends ChajianUnitapi
{
	/**
	*	保存设置
	*/
	public function postsavecog($request)
	{
		$copt = $this->getNei('option');
		$this->getNei('Wxqy:dept')->cleartoken();
		$copt->setval('weixinqy_corpid', nulltoempty($request->input('wxqycorpid')));
		$copt->setval('weixinqy_secret', nulltoempty($request->input('wxqysecret')));
		$copt->setval('weixinqy_depid', nulltoempty($request->input('wxqydepid')));
		$copt->setval('weixinqy_todo', nulltoempty($request->input('wxqytodo')));
		$copt->setval('weixinqy_huitoken', nulltoempty($request->input('wxqyhuitoken')));
		$copt->setval('weixinqy_aeskey', nulltoempty($request->input('wxqyaeskey')));
		
		return returnsuccess();
	}
	
	private function returnwx($barr)
	{
		if($barr['errcode']!=0)return returnerror(''.$barr['errcode'].':'.$barr['msg'].'');
		return returnsuccess();
	}
	
	
	
	public function getwdept()
	{
		$barr = $this->getNei('Wxqy:dept')->getdeptlist();
		return $this->returnwx($barr);
	}
	
	public function anaytosys()
	{
		$barr = $this->getNei('Wxqy:dept')->anaytosys();
		return $this->returnwx($barr);
	}
	
	public function deldept($request)
	{
		$id	  = (int)$request->get('id','0');
		$barr = $this->getNei('Wxqy:dept')->deletedept($id);
		return $this->returnwx($barr);
	}
	
	public function updatedept($request)
	{
		$id	  = (int)$request->get('id','0');
		$barr = $this->getNei('Wxqy:dept')->updatedept($id);
		return $this->returnwx($barr);
	}
	
	//-------------用户相关---------------
	public function getuserlist()
	{
		$barr = $this->getNei('Wxqy:user')->getuserlist();
		return $this->returnwx($barr);
	}
	
	public function delaluser()
	{
		$barr = $this->getNei('Wxqy:user')->delaluser();
		return $this->returnwx($barr);
	}
	public function anaytouser()
	{
		$barr = $this->getNei('Wxqy:user')->anaytouser();
		return $this->returnwx($barr);
	}
	
	public function updateuser($request)
	{
		$id	  = (int)$request->get('id','0');
		$barr = $this->getNei('Wxqy:user')->updateuser($id);
		return $this->returnwx($barr);
	}
	public function deleteuser($request)
	{
		$sid	  = $request->get('sid');
		if(isempt($sid))return returnerror('没有选择用户');
		$barr = $this->getNei('Wxqy:user')->deleteuser($sid);
		return $this->returnwx($barr);
	}	
	
	
	//---应用---
	public function postagentsave($request)
	{
		$id 		= (int)$request->input('id','0');
		$agenhid 	= (int)$request->input('agenhid','0');
		$agentid	= trim(nulltoempty($request->input('agentid')));
		$home_url	= '';
		if($agenhid>0){
			$fors 		= AgenhModel::where(['cid'=>$this->companyid,'id'=>$agenhid])->first();
			if(!$fors)return returnerror('关联系统应用不存在');
			$home_url	= config('app.urly').'/ying/'.$this->companyinfo->num.'/'.$fors->num.'?agentid='.$agentid.'';
		}
		$secret		= trim(nulltoempty($request->input('secret')));
		if(strlen($secret)<40)return returnerror('应用secret格式错误');
		
		$obj = WxqyagentModel::where(['cid'=>$this->companyid,'id'=>$id])->first();
		if(!$obj){
			$obj = new WxqyagentModel();
		}
		
		if($agenhid>0)$obj->home_url 	= $home_url;
		
		$obj->cid 		= $this->companyid;
		if($agenhid>0){
			$obj->name 		= $fors->name;
			$obj->description 		= $fors->description;
		}else{
			$obj->name 	= nulltoempty($request->input('name'));
		}
		
		$obj->agentid 	= $agentid;
		$obj->secret 	= $secret;
		$obj->agenhid 	= $agenhid;
		$obj->save();
		
		$obj  = $this->getNei('Wxqy:agent');
		$obj->cleartoken($agentid, true);
		
		$barr = $obj->getagent($agentid);
		$msg  = '';
		if($barr['errcode']!=0)$msg = $barr['msg'];
		
		return returnsuccess('保存成功'.$msg.'');
	}
	
	public function postagentdel($request)
	{
		$id 		= (int)$request->input('id','0');
		WxqyagentModel::where(['cid'=>$this->companyid,'id'=>$id])->delete();
		return returnsuccess();
	}
	public function getagent($request)
	{
		$agentid 		= (int)$request->input('agentid','0');
		$barr = $this->getNei('Wxqy:agent')->getagent($agentid);
		return $this->returnwx($barr);
	}
	public function setagent($request)
	{
		$agentid 		= (int)$request->input('agentid','0');
		$barr = $this->getNei('Wxqy:agent')->setagent($agentid);
		return $this->returnwx($barr);
	}
	public function menuupdate($request)
	{
		$agentid 		= (int)$request->input('agentid','0');
		$barr = $this->getNei('Wxqy:agent')->menuupdate($agentid);
		return $this->returnwx($barr);
	}
	public function menuclear($request)
	{
		$agentid 		= (int)$request->input('agentid','0');
		$barr = $this->getNei('Wxqy:agent')->menudelete($agentid);
		return $this->returnwx($barr);
	}
	public function getmenu($request)
	{
		$agentid 		= (int)$request->input('agentid','0');
		$barr = $this->getNei('Wxqy:agent')->getmenu($agentid);
		return $this->returnwx($barr);
	}
	public function menuse($request)
	{
		$agentid 		= (int)$request->input('agentid','0');
		$barr = $this->getNei('Wxqy:agent')->menuse($agentid);
		return $this->returnwx($barr);
	}
	public function postsavemenu($request)
	{
		$id 		= (int)$request->input('id','0');
		$menujson 		= nulltoempty($request->input('menujson'));
		WxqyagentModel::where(['cid'=>$this->companyid,'id'=>$id])->update([
			'menujson'=>$menujson
		]);
		return returnsuccess();
	}
	//发应用消息测试
	public function postsendagent($request)
	{
		$msg 			= $request->input('msg');
		$name2 			= $request->input('name');
		$barr = $this->getNei('Wxqy:index')->sendtext($this->useaid, $name2, $msg);
		return $this->returnwx($barr);
	}
}