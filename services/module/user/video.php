<?php
$cache_file = getTplCache('services/user/video');
FanweService::instance()->cache->loadCache('albums');

if(!@include($cache_file))
{
	$login_modules = getLoginModuleList();
	include template('services/user/video');
}
display($cache_file);
?>