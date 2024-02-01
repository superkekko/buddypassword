<?php
class privatepages extends authentication {
	function beforeroute($f3) {

		if (!$this->checklogged($f3)) {
			$f3->reroute("/logout");
		}
		
		$current_user = $f3->get('active_user');
		
		$lists_raw = $f3->get('DB')->exec("SELECT distinct list FROM password where user_ins = ? or (group_id = ? and share = ?)", array($current_user['user_id'], $current_user['group_id'], 1));
		$lists = [];
		foreach ($lists_raw as $item) {
			$lists[] = $item['list'];
		}
		$lists = array_unique($lists);
		asort($lists);
		$f3->set('list', $lists);

		$tags_raw = $f3->get('DB')->exec("SELECT distinct tags FROM password where user_ins = ? or (group_id = ? and share = ?)", array($current_user['user_id'], $current_user['group_id'], 1));
		$tags = [];
		foreach ($tags_raw as $item) {
			foreach (explode(',', $item['tags']) as $subitem) {
				$tags[] = $subitem;
			}
		}
		$tags = array_unique($tags);
		asort($tags);
		$f3->set('tags', $tags);
	}

	function afterroute($f3) {
		$f3->set('site_url', $this->siteURL());
		echo Template::instance()->render('private-layout.html');
	}

	function allview($f3) {
		$current_user = $f3->get('active_user');

		$results = $f3->get('DB')->exec("SELECT * FROM password where user_ins = ? or (group_id = ? and share = ?)", array($current_user['user_id'], $current_user['group_id'], 1));
		$allitem = [];
		foreach ($results as $result) {
			$result['password'] = $this->encriptDecript($f3, $result['password'], 'd');
			$allitem[] = $result;
		}
		$f3->set('password', $allitem);
		$f3->set('content', 'private-item.html');
	}

	function lists($f3) {
		$current_user = $f3->get('active_user');

		$results = $f3->get('DB')->exec("SELECT distinct list FROM password where user_upd = ? or (group_id = ? and share = ?)", array($current_user['user_id'], $current_user['group_id'], 1));
		$alllist = [];
		foreach ($results as $result) {
			$alllist[] = $result['list'];
		}

		if (($key = array_search("", $alllist)) !== false) {
			unset($alllist[$key]);
		}

		$f3->set('list_item', $alllist);

		$f3->set('content', 'private-list.html');
	}

	function listview($f3) {
		$current_user = $f3->get('active_user');
		$id = $f3->get('PARAMS.id');

		$results = $f3->get('DB')->exec("SELECT * FROM password where (user_upd = ? or (group_id = ? and share = ?)) and list = ?", array($current_user['user_id'], $id));

		$f3->set('list', $results);
		$f3->set('content', 'private-item.html');
	}

	function tags($f3) {
		$current_user = $f3->get('active_user');

		$results = $f3->get('DB')->exec("SELECT distinct tags FROM password where user_upd = ? or (group_id = ? and share = ?)", array($current_user['user_id'], $current_user['group_id'], 1));
		$alltags = [];

		foreach ($results as $result) {
			$alltags = array_merge($alltags, explode(',', $result['tags']));
		}

		if (($key = array_search("", $alltags)) !== false) {
			unset($alltags[$key]);
		}

		$f3->set('list_item', array_unique($alltags));

		$f3->set('content', 'private-tag.html');
	}

	function tagview($f3) {
		$current_user = $f3->get('active_user');
		$id = $f3->get('PARAMS.id');

		$results = $f3->get('DB')->exec("SELECT * FROM password t where (user_upd = ? or (group_id = ? and share = ?)) and (',' || t.tags || ',') LIKE ?", array($current_user['user_id'], '%,'.$id.',%'));

		$f3->set('list', $results);
		$f3->set('content', 'private-item.html');
	}

	function edit($f3) {
		$current_user = $f3->get('active_user');

		$id = $f3->get('POST.id');
		$task = trim($f3->get('POST.task'));
		
		if(!empty($f3->get('POST.share'))){
			$share = 1;
		}else{
			$share = 0;
		}

		if ($task == 'delete') {
			$f3->get('DB')->exec("DELETE FROM password WHERE id=?", $id);
		} elseif ($task == 'edit') {
			if ($f3->get('POST.tags') == '') {
				$tags = null;
			} else {
				$tags = implode(',', $f3->get('POST.tags'));
			}

			if ($id == 0) {
				$f3->get('DB')->exec("INSERT INTO password (name, user, password, link, tags, list, group_id, share, user_ins, time_ins, user_upd, time_upd) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)", array($f3->get('POST.name'), $f3->get('POST.user'), $this->encriptDecript($f3, $f3->get('POST.password')), $f3->get('POST.link'), $tags, $f3->get('POST.list'), $current_user['group_id'], $share, $current_user['user_id'], date("Y-m-d H:i:s"), $current_user['user_id'], date("Y-m-d H:i:s")));
			} else {
				$f3->get('DB')->exec("UPDATE password SET name=?, user=?, password=?, link=?, tags=?, list=?, share=?, user_upd=?, time_upd=? WHERE id=?", array($f3->get('POST.name'), $f3->get('POST.user'), $this->encriptDecript($f3, $f3->get('POST.password')), $f3->get('POST.link'), $tags, $f3->get('POST.list'), $share, $current_user['user_id'], date("Y-m-d H:i:s"), $id));
			}
		} elseif ($task == 'setup-password'){
			$f3->get('DB')->exec("UPDATE user set private_key = ? where user_id=?", array($f3->get('POST.check-value'), $current_user['user_id']));
		}

		$f3->reroute($f3->get('URI'));
	}
}