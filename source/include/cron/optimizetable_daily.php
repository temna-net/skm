<?php
if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}
$query = DB::query("SHOW TABLE STATUS");
while($table = DB::fetch($query)) 
if($table[Data_free]>0 && $table[Name]!=$tablepre.'sessions')
DB::query("OPTIMIZE TABLE $table[Name]");
?>
