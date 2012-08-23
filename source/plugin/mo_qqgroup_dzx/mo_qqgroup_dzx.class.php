<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_mo_qqgroup_dzx {

	var $isopen = '';
	var $button = '';
	var $position = '';

	function plugin_mo_qqgroup_dzx() {
		global $_G;
		$mo_cfg = $_G['cache']['plugin']['mo_qqgroup_dzx'];
		$this->isopen = $mo_cfg['isopen'];
		$this->position = $mo_cfg['gposition'];
		$this->floatpos = $mo_cfg['floatpos'];
		$btnsize = $mo_cfg['gbtn_size'];
		$btnsize = $btnsize==2?'mobtns':'mobtn';
		$this->floatlr = $this->position==4?'left':'right';
		
		if($this->isopen){
			if($this->position<=1){
				$this->button='<script type="text/javascript"> var node = document.getElementById("qmenu"); var insertedNode = document.createElement("em"); insertedNode.style.display = \'inline\'; insertedNode.style.styleFloat = \'right\'; insertedNode.style.cssFloat = \'right\'; insertedNode.style.lineHeight = \'26px\'; insertedNode.style.margin = \'4px 0 0\'; insertedNode.innerHTML = \'<em onclick=showWindow("mo_qqgroup_dzx","plugin.php?id=mo_qqgroup_dzx") title="'.lang('plugin/mo_qqgroup_dzx', 'mogroup_title').'" style="cursor:pointer;"><img src="source/plugin/mo_qqgroup_dzx/icon/'.$btnsize.'.png" border="0"></em>\'; node.parentNode.insertBefore(insertedNode, node.nextSibling); </script>';
			}elseif($this->position>3){
				$this->button='<a href="plugin.php?id=mo_qqgroup_dzx" onclick="showWindow(\'mo_qqgroup_dzx\', this.href)" title="'.lang('plugin/mo_qqgroup_dzx', 'mogroup_title').'"><img src="source/plugin/mo_qqgroup_dzx/icon/'.$this->floatlr.'.png" border="0"></a>';
			}else{
				$this->button='<a href="plugin.php?id=mo_qqgroup_dzx" onclick="showWindow(\'mo_qqgroup_dzx\', this.href)" title="'.lang('plugin/mo_qqgroup_dzx', 'mogroup_title').'"><img src="source/plugin/mo_qqgroup_dzx/icon/'.$btnsize.'.png" border="0"></a>';
			}
		}
	}

	function global_header() {
		if($this->position<=1) return $this->button;
	}
	function global_cpnav_extra1() {
		if($this->position==2) return $this->button;
	}
	function global_cpnav_extra2() {
		if($this->position==3) return $this->button;
	}
	function global_footer() {
		if($this->position>3) return '<div style="position:fixed; '.$this->floatlr.':0; top:'.$this->floatpos.'%;">'.$this->button.'</div>';
	}
}
?>