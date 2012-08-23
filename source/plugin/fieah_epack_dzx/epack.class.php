<?php
/*
 * =============================================
 * Discuz!X2 Extend pack
 * ---------------------------------------------
 * Fieah Workshop [www.fieah.com] (C)Forever.
 * Author: Kinwei 'Fieah' Lim
 * Email : f@12345.la
 * For Ashlyn Cheng & DDB, love you all.
 * =============================================
*/

!defined('IN_DISCUZ') && exit('Access Denied');

class plugin_fieah_epack_dzx {

	var $epack = array();

	function plugin_fieah_epack_dzx() {
		global $_G;
		$this->epack['config'] = $_G['cache']['plugin']['fieah_epack_dzx'];
		$this->epack['allowtool'] = $this->epack['config']['allowtool'] && $_G['adminid'] == 1;
		$this->epack['fid'] = $_G['fid'] ? $_G['fid'] : 0;
		$this->epack['groupid'] = $_G['groupid'];
		$this->epack['ismoderator'] = $_G['forum']['ismoderator'];
	}

	function _lang($var) {
		return lang('plugin/fieah_epack_dzx', $var);
	}

	function _unserialize($val, $conf, $empty = false) {
		$data = (array)unserialize($this->epack['config'][$conf]);
		return $empty ? (empty($data[0]) ? true : $this->_unserialize($val, $conf)) : (in_array($val, $data) ? true : false);
	}

	function _isfounder() {
		global $_G;
		$founders = explode(',', str_replace(' ', '', $_G['config']['admincp']['founder']));
		return in_array($_G['uid'], $founders) || (!is_numeric($_G['username']) && in_array($_G['username'], $founders)) ? true : false;
	}

	function _tool($top) {
		global $_G;
		!$top && $isfounder = $this->_isfounder();
		include template('fieah_epack_dzx:tool');
		return $return;
	}

	function global_usernav_extra2() {
		$return = $this->epack['allowtool'] ? $this->_tool(true) : '';
		return $return;
	}

	function global_footer() {
		$return = '';
		$this->epack['config']['disblink'] && $return .= '<script>function noticeTitle() { return false; }</script>';
		$this->epack['allowtool'] && $_GET['tool'] && $return .= $this->_tool();
		return $return;
	}

	function common() {
		if($this->epack['allowtool']) {
			if(submitcheck('epack_viewglobalsubmit') && $this->_isfounder()) {
				debug(isset($GLOBALS[$_POST['epack_viewglobal']]) ? $GLOBALS[$_POST['epack_viewglobal']] : $GLOBALS['_G']);
			}

			if(submitcheck('epack_cachesubmit')) {
				require_once libfile('function/cache');
				if(in_array('data', $_POST['epack_cache'])) {
					updatecache();
				}
				if(in_array('tpl', $_POST['epack_cache'])) {
					updatecache('styles');
					$tpl = dir(DISCUZ_ROOT.'./data/template');
					while($entry = $tpl->read()) {
						if(preg_match("/\.tpl\.php$/", $entry)) {
							@unlink(DISCUZ_ROOT.'./data/template/'.$entry);
						}
					}
					$tpl->close();
				}
				if(in_array('block', $_POST['epack_cache'])) {
					require_once libfile('function/block');
					blockclass_cache();
				}
				exit('<script>parent.$(\'epack_waitid\').innerHTML = \'<img src="\' + parent.IMGDIR + \'/check_right.gif" class="vm"> '.$this->_lang('cachedone').'\';</script>');
			}
		}
	}

}

class plugin_fieah_epack_dzx_forum extends plugin_fieah_epack_dzx {

	function forumdisplay_highlight_output() {
		if(!IS_ROBOT && $this->epack['config']['selfhighlight']) {
			global $_G;
			foreach($_G['forum_threadlist'] as $key => $thread) {
				($thread['authorid'] == $_G['uid'] && !in_array($thread['displayorder'], array(1, 2, 3))) && $_G['forum_threadlist'][$key]['subject'] = '<b>'.$thread['subject'].'</b>';
			}
		}
	}

	function viewthread_top_output() {
		$return = '<style>';
		$this->epack['config']['adjustpls'] > 160 && $return .= '.pls { width: '.$this->epack['config']['adjustpls'].'px; }';
		!$this->_unserialize($this->epack['fid'], 'allowcommentforums', true) && !$this->epack['ismoderator'] && $return .= '.cmmnt { display: none; }';
		$return .= '</style>';
		return $return;
	}

	function _viewthread_modarea($pos) {
		$return = $modarea = array();
		if(!IS_ROBOT) {
			if(!@include(DISCUZ_ROOT.'./data/cache/cache_epack.php')) {
				require_once libfile('function/cache');
				include libfile('cache/epack', 'function');
				$modarea = build_cache_epack(true);
			}

			if(!in_array('', $modarea)) {
				global $postlist;
				foreach($postlist as $key => $post) {
					$return[] = $post['anonymous'] || (!$post['authorid'] && !$post['username']) || !isset($modarea[$post['authorid']]) ? '' : ($pos == 1 ? '<fieldset class="pil modarea"><legend class="xi2 xw1"><img src="static/image/common/access_normal.gif" /> '.$this->_lang('modarea').'</legend>'.$modarea[$post['authorid']].'</fieldset>' : '<a href="javascript:;" class="showmenu pil" id="modarea_'.$key.'" onmouseover="showMenu(this.id);">'.$this->_lang('modarea').'</legend></a><div id="modarea_'.$key.'_menu" class="p_pop" style="display: none">'.$modarea[$post['authorid']].'</div>');
				}
			}
		}
		return $return;
	}

	function viewthread_imicons_output() {
		return $this->epack['config']['plsmodarea'] == 1 ? $this->_viewthread_modarea(1) : array();
	}

	function viewthread_sidebottom_output() {
		return $this->epack['config']['plsmodarea'] == 2 ? $this->_viewthread_modarea(2) : array();
	}

	function viewthread_postheader_output() {
		$return = array();
		if(!IS_ROBOT && $this->epack['config']['authororself']) {
			global $_G, $postlist;
			$tag = '<span class="pipe">|</span><em class="xw1';
			foreach($postlist as $post) {
				$return[] = !$post['anonymous'] || ($post['authorid'] && $post['username']) ? ($_G['uid'] == $post['authorid'] ? $tag.' xi1">('.$this->_lang('self').')</em>' : ($_G['thread']['authorid'] == $post['authorid'] ? $tag.'" style="color: #800000">('.$this->_lang('author').')</em>' : '')) : '';
			}
		}
		return $return;
	}

	function post_side_bottom_output() {
		global $isfirstpost;
		$return = '';
		if($_GET['action'] == 'newthread' || $_GET['action'] == 'edit' && $isfirstpost) {
			$return .= '<script>';
			$this->_unserialize($this->epack['fid'], 'postreadpermforums') && !$this->epack['ismoderator'] && $return .= '$(\'readperm\').value = 255;';
			!$this->_unserialize($this->epack['groupid'], 'allowhiddenreplygroups', true) && !$this->epack['ismoderator'] && $return .= '$(\'hiddenreplies\').disabled = true;';
			$return .= '</script>';
		}
		return $return;
	}

	function post_permvalue() {
		global $_G;
		$this->_unserialize($this->epack['fid'], 'postreadpermforums') && !$this->epack['ismoderator'] && $_G['gp_readperm'] = 255;
		!$this->_unserialize($this->epack['groupid'], 'allowhiddenreplygroups', true) && !$this->epack['ismoderator'] && $_G['gp_hiddenreplies'] = 0;
	}

	function misc_allowcomment() {
		$_GET['action'] == 'comment' && !$this->_unserialize($this->epack['fid'], 'allowcommentforums', true) && !$this->epack['ismoderator'] && showmessage('forum_access_view_disallow');
	}

}


?>