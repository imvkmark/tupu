<?php
function bindCacheImageservers()
{
	$water_mark = FDB::resultFirst("SELECT val FROM ".FDB::table('sys_conf')." WHERE name = 'WATER_MARK'");
	
	$list = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('share_image_sizes')." WHERE status = 1");
	while($data = FDB::fetch($res))
	{
		if($water_mark == 0)
			$data['is_water'] = 0;
			
		$list[] = array($data['width'],$data['height'],$data['is_cut'],$data['is_water']);
	}
	FanweService::instance()->cache->saveCache('image_sizes', $list);

	$list = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('image_servers')." order by upload_count ASC");
	while($data = FDB::fetch($res))
	{
		$parse_url = parse_url($data['url']);
		$scheme = $parse_url['scheme'];
		$host = $parse_url['host'];
		$port = (int)$parse_url['port'];
		$path = $parse_url['path'];

		$data['host'] = $host;
		$data['host_port'] = $host;
		if($port > 0)
		{
			$data['port'] = $port;
			$data['host_port'] .= ':'.$port;
		}
		else
			$data['port'] = 80;

		if(!empty($path))
		{
			$paths = explode('/',$path);
			$path = '/';
			foreach($paths as $pval)
			{
				if(!empty($pval))
					$path .= $pval.'/';
			}
		}
		else
			$path = '/';

		$data['path'] = $path;
		$data['url'] = $scheme.'://'.$data['host_port'].$path;
		$list['all'][$data['code']] = $data;
		if($data['status'] == 1)
			$list['active'][$data['code']] = $data;
	}
	FanweService::instance()->cache->saveCache('image_servers', $list);
}
?>