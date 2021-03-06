<?php
$result = array();
$result['status'] = 0;

$data = array(
	'title' => trim($_FANWE['request']['title']),
	'cid' => (int)$_FANWE['request']['cid'],
);

if($data['title'] == '输入新专辑名')
	exit;

$vservice = FS('Validate');
$validate = array(
	array('title','required',lang('album','name_require')),
	array('title','max_length',lang('album','name_max'),60),
	array('cid','min',lang('album','cid_min'),1),
);

if(!$vservice->validation($validate,$data))
{
	$result['msg'] = $vservice->getError();
	outputJson($result);
}
FanweService::instance()->cache->loadCache('albums');
if(!isset($_FANWE['cache']['albums']['category'][$data['cid']]))
	exit;


$check_result = FS('Share')->checkWord($_FANWE['request']['title'],'title');
if($check_result['error_code'] == 1)
{
	$result['msg'] = $check_result['error_msg'];
	outputJson($result);
}
//判断不能有相同的名称
$re=FDB::resultFirst("select count(*) from ".FDB::table('album')." where uid=".$_FANWE['uid']." and title='".$data['title']."'");
if($re>=1){
	$result['msg']='名称重复，请重新填写';
	echo outputJson($result);
}
$_FANWE['request']['uid'] = $_FANWE['uid'];
$_FANWE['request']['type'] = 'album';
$_FANWE['request']['content'] = $_FANWE['request']['title'];
$share = FS('Share')->submit($_FANWE['request'],false,true,true);
if($share['status'])
{
	$data['title'] = htmlspecialchars($_FANWE['request']['title']);
	$data['album_title_match'] = segmentToUnicode(clearSymbol($data['title']));
	$data['uid'] = $_FANWE['uid'];
	$data['share_id'] = $share['share_id'];
	$data['create_day'] = getTodayTime();
	$data['create_time'] = TIME_UTC;
	$data['show_type'] = 2;
	
	$aid = FDB::insert('album',$data,true);
	
	FDB::query('UPDATE '.FDB::table('share').' SET rec_id = '.$aid.' 
		WHERE share_id = '.$share['share_id']);
	FDB::query("update ".FDB::table("user_count")." set albums = albums + 1 where uid = ".$_FANWE['uid']);
	$result['url'] = FU("album/show",array('id'=>$aid));
	$result['aid'] = $aid;
	$result['title'] = $data['title'];
	$result['status'] = 1;
}
else
	$result['msg'] = '添加数据失败';

outputJson($result);
?>