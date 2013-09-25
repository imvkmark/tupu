<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq <awfigq@qq.com>
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * 后台导航
 +------------------------------------------------------------------------------
 */
class RoleNavAction extends CommonAction
{
	public function add()
	{
		$new_sort = D(MODULE_NAME)->max("sort") + 1;
		$this->assign('new_sort',$new_sort);
		$this->display();
	}
}
?>