<?php
/*
	hx_upgraderate By hengxian.cn 2010-10-13
*/
class plugin_hx_upgraderate
{

}
class plugin_hx_upgraderate_forum extends plugin_hx_upgraderate
{
	function viewthread_sidetop_output()
	{
		global $postlist,$_G;
		
		$var = $_G['cache']['plugin']['hx_upgraderate'];
		
		if($var['open']==false)
		{
			return;
		}
		
		$list=array();
		foreach($postlist as $key => $val)
		{
			
			if($val['upgradecredit']==true)
			{
				$creditslower = $_G['cache']['usergroups'][$val['groupid']]['creditslower'];
				$creditshigher = $_G['cache']['usergroups'][$val['groupid']]['creditshigher'];
				$credits = $val['credits'];
				
				$rate =round(($credits - $creditshigher) / ($creditslower - $creditshigher) * 100,0);
				$list[] = $creditslower ? '<div style="margin:5px 10px 5px 20px;" id="_g_up'.$val[pid].'" onmouseover="showMenu({\'ctrlid\':this.id,\'menuid\':\'g_up'.$val[pid].'_menu\', \'pos\':\'12\'});"><div style="float:left;margin-right:5px;">'.$var['title'].'</div><div style="padding-top:2px;float:left;"><img width="2" height="12" src="source/plugin/hx_upgraderate/images/expl.gif"><img width="'.round($rate * 0.6,0).'" height="12" src="source/plugin/hx_upgraderate/images/expc.gif"><img width="6" height="12" src="source/plugin/hx_upgraderate/images/expr.gif"></div><div>'.$rate.'%</div></div>' : '';
			}
			else
			{
				$list[] = "";
			}
			

		}
		return $list;
	}
}
?>