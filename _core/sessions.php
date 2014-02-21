<?php



/**
 * 
 */
class sessions
{
	/**
	 * <p>這個公開的屬性是一個mysqli::fetch_assoc這種陣列只有關鍵索引沒有數字索引</p>
	 * <p>在這個陣列內容中會存放 sessions 與 user 資料表結合的當前瀏覽者陣列資料</p>
	 */
	public $data = array();
	
	private $_ip = '';
	private $_session_id = '';
	private $_time_now = 0;
	private $_script_name = '';
	private $_cookie_expire = 0;
	
	private $_guid_pattern = '/^[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}$/'; // 用於驗證GUID的正規表示式
	
	/**
	 * 這個屬性是DateTime物件
	 */
	public $datetime;
	
	
	function __construct()
	{
		global $conf;
		$this->datetime = new DateTime(null, new DateTimeZone($conf->data['timezone']));
	}
	
	/**
	 * 這個方法的使用如同php的date
	 * @param unknown $format 時間格式
	 * @param unknown $timestamp 時間戳記
	 */
	function date($format, $timestamp)
	{
		global $conf;
		$datetime = new DateTime(null, new DateTimeZone($conf->data['timezone']));
		$datetime->setTimestamp($timestamp);
		return $datetime->format($format);
	}
	
	/**
	 * 啟動 session
	*/
	public function begin()
	{
		global $conf, $db;

		$this->_time_now = time();
		$this->_cookie_expire = $this->_time_now + (int)$conf->data['cookie_expire'];
		$remote_addr = ks::external_var('REMOTE_ADDR', ks::SERVER, true);
		$script_name = ks::external_var('SCRIPT_NAME', ks::SERVER, true);
		$this->_ip = (filter_var($remote_addr, FILTER_VALIDATE_IP)) ? $remote_addr : '';
		$this->_script_name = ($script_name) ? $script_name : '';
		
		$this->gc();//釋放逾時session
		

		$cookie_sid = ks::external_var($conf->data['cookie_name'] . '_sid', ks::COOKIE, true);
		$cookie_uid = ks::external_var($conf->data['cookie_name'] . '_uid', ks::COOKIE, true);
		$cookie_kid = ks::external_var($conf->data['cookie_name'] . '_kid', ks::COOKIE, true);
		($cookie_uid) ? $cookie_uid = intval($cookie_uid) : '';

		if ($cookie_sid)
		{
			$this->_session_id = '';
			$this->_session_id = $this->_check_sid($cookie_sid);
			if(!empty($this->_session_id))
				$this->_write_data();
		}

		// 如果目前瀏覽端有設定kid跟uid 但sid是空的時 (處理自動登入)
		if ($cookie_uid != 0 && $cookie_kid && empty($this->_session_id))
		{
			if(preg_match($this->_guid_pattern, $cookie_kid))
			{
				$sql = "SELECT * FROM " . T_SESSIONS_KEYS . " WHERE user_id = :user_id AND key_id = :key_id";
				if($sth = $db->prepare($sql))
				{
					$sth->bindParam(':user_id', $cookie_uid, PDO::PARAM_INT);
					$sth->bindParam(':key_id', $cookie_kid, PDO::PARAM_STR);
					$sth->execute();
					$count = $sth->rowCount();
					$data = $sth->fetchObject();
					
					$sth->closeCursor();
					if($count === 1)
					{
						$this->_session_id = new_guid();
						$this->set_cookie($conf->data['cookie_name'] . '_sid', $this->_session_id, $this->_cookie_expire);
						$this->set_cookie($conf->data['cookie_name'] . '_uid', $data->user_id, $this->_cookie_expire);
						$this->set_cookie($conf->data['cookie_name'] . '_kid', $data->key_id, $this->_cookie_expire);
							
						$this->insert_new_sid($data->user_id, $this->_session_id, $this->_ip, $this->_script_name);
						$this->_up_keys_last_login($data->key_id);// 更新keys最後登入時間
						//TODO 在這邊應該要加入 自動登入後也應該要針對該UID的最後登入時間欄位刷新時間
						$this->_write_data();
					}
				} else trigger_error($sql, E_USER_ERROR);
			}
		}
		
		if(empty($this->_session_id))
		{
			$this->_session_id = new_guid();
			
			$this->set_cookie($conf->data['cookie_name'] . '_sid', $this->_session_id, $this->_cookie_expire);
			$this->set_cookie($conf->data['cookie_name'] . '_uid', '1', $this->_cookie_expire);
			$this->set_cookie($conf->data['cookie_name'] . '_kid', '', $this->_cookie_expire);
			
			$this->insert_new_sid(1, $this->_session_id, $this->_ip, $this->_script_name);
			
			$this->_write_data();
		}
		
		$this->_updata_session();
		

	}


	
	/**
	 * <p>登入的方法，需要帳號密碼，自動登入的參數是可選</p>
	 * <p>如果登入成功將返回該用戶的uid如果查詢不正確則返回 0 如果這個帳號沒有被啟用會返回-1</p>
	 * @param name
	 * @param password
	 * @param [auto]
	 * @return int
	 */
	public function login($name, $password, $autologin = false)
	{
		global $db, $conf;
		$uid = 0;
		$name = trim($name);
		$password = trim($password);
		$uid = $this->_verification($name, $password);

		if($uid > 1)
		{
			$sql = "UPDATE " . T_SESSIONS . " SET session_user_id = " . $uid . " WHERE session_id = '" . $this->_session_id . "'";
			if (!$db->query($sql))
				trigger_error($sql, E_USER_ERROR);
			$this->set_cookie($conf->data['cookie_name'] . '_uid', $uid, $this->_cookie_expire);
			
			$this->_up_last_login($uid);// 更新最後登入時間
			
			if($autologin)
			{
				$kid = new_guid();
				$this->insert_new_kid($kid, $uid);
				$this->set_cookie($conf->data['cookie_name'] . '_kid', $kid, $this->_cookie_expire);
			}
			
			$this->_write_data();// 為了讓登入後立即可以使用$data屬性
		}
		return $uid;
	}
	
	/**
	 * 這個方法會將當前的瀏覽端sessions的 session_user_id 改回 1 也就是變成訪客
	 * @return boolean 如果返回假表示該session目前並不是登入狀態
	 */
	public function logout()
	{
		global $db, $conf;
		if((int)$this->data['user_id'] > 1)
		{
			$sql = "UPDATE " . T_SESSIONS . " SET session_user_id = 1 WHERE session_id = '" . $this->_session_id . "'";
			if (!$db->query($sql))
				trigger_error($sql, E_USER_ERROR);
			$this->set_cookie($conf->data['cookie_name'] . '_sid', $this->_session_id, $this->_cookie_expire);
			$this->set_cookie($conf->data['cookie_name'] . '_uid', '1', $this->_cookie_expire);
			$this->set_cookie($conf->data['cookie_name'] . '_kid', '', $this->_cookie_expire);
			return true;
		}
		return false;
	}
	
	/**
	 * 這個方法用來刪除(釋放)超過一定時間的session
	 * 釋放的時間取決於 $conf->data['session_length']
	 * @return void
	 */
	public function gc()
	{
		global $db, $conf;
		$killtime = (time() - (int)$conf->data['session_length']);
		$sql = "DELETE FROM " . T_SESSIONS . " WHERE session_last_time < " . $killtime;
		if (!$db->query($sql))
			trigger_error($sql, E_USER_ERROR);
	}
	
	/**
	 * 設定cookie
	 * @param name 預設定cookie名稱
	 * @param cookiedata 要寫入的資料
	 * @param cookietime 有效時限
	 * @return void
	 */
	public function set_cookie($name, $cookiedata, $cookietime)
	{
		global $conf;

		$name_data = rawurlencode($name) . '=' . rawurlencode($cookiedata);
		$expire = gmdate('D, d-M-Y H:i:s \\G\\M\\T', $cookietime);
		$domain = (!$conf->data['cookie_domain'] || $conf->data['cookie_domain'] == 'localhost' || $conf->data['cookie_domain'] == '127.0.0.1') ? '' : '; domain=' . $conf->data['cookie_domain'];

		header('Set-Cookie: ' . $name_data . (($cookietime) ? '; expires=' . $expire : '') . '; path=' . $conf->data['cookie_path'] . $domain . ((!$conf->data['cookie_secure']) ? '' : '; secure') . '; HttpOnly', false);
	}
	
	/**
	 * 寫入一筆新的資料到 sessions 資料表
	 * @param int user_id
	 * @param string sid
	 * @param string ip
	 * @param srting page
	 * @return boolean
	 */
	public function insert_new_sid($user_id, $sid, $ip, $page)
	{
		global $db;
		$sql = "INSERT INTO " . T_SESSIONS . " 
				(session_id, session_user_id, session_start, session_last_time, session_ip, session_page)
				VALUES
				(:session_id, :session_user_id, :session_start, :session_last_time, :session_ip, :session_page)";
		if ($sth = $db->prepare($sql))
		{
			$sth->bindParam(':session_id', $sid, PDO::PARAM_STR);
			$sth->bindParam(':session_user_id', $user_id, PDO::PARAM_INT);
			$sth->bindParam(':session_start', $this->_time_now, PDO::PARAM_INT);
			$sth->bindParam(':session_last_time', $this->_time_now, PDO::PARAM_INT);
			$sth->bindParam(':session_ip', $ip, PDO::PARAM_STR);
			$sth->bindParam(':session_page', $page, PDO::PARAM_STR);
			$sth->execute();
		} else trigger_error($sql, E_USER_ERROR);

		return true;
	}
	
	/**
	 * 寫入一筆新的資料到 sessions_keys 資料表
	 * @param string $key_id
	 * @param int $user_id
	 * @return boolean
	 */
	public function insert_new_kid($key_id, $user_id)
	{
		global $db;
		if(!preg_match($this->_guid_pattern, $key_id))
			trigger_error($key_id . ' not guid', E_USER_ERROR);
		
		$sql = "INSERT INTO " . T_SESSIONS_KEYS . "
				(key_id, user_id, add_time, last_login, last_ip)
				VALUES
				(:key_id, :user_id, :add_time, :last_login, :last_ip)";
		if ($sth = $db->prepare($sql))
		{
			$sth->bindParam(':key_id', $key_id, PDO::PARAM_STR);
			$sth->bindParam(':user_id', $user_id, PDO::PARAM_INT);
			$sth->bindParam(':add_time', $this->_time_now, PDO::PARAM_INT);
			$sth->bindParam(':last_login', $this->_time_now, PDO::PARAM_INT);
			$sth->bindParam(':last_ip', $this->_ip, PDO::PARAM_STR);
			$sth->execute();
		} else trigger_error($sql, E_USER_ERROR);
	
		return true;
	}
	
	/**
	 * <p>依據參數查詢帳號與密碼是否相符(帳號啟用也須開啟 user_enable = 'Y')</p>
	 * <p>如查詢比對正確將返回該用戶的uid如果查詢不正確則返回 0 如果這個帳號沒有被啟用會返回-1</p>
	 * <p>這個功能目前仍需改良，因為目前為止這功能的進入參數值都沒有加入任何過濾</p>
	 * @param $username 帳號
	 * @param $password 密碼
	 * @return int 會員ID
	 */
	private function _verification($username, $password)
	{
		global $db;
		$username = trim($username);
		$password = md5(trim($password));
		$sql = 'SELECT user_id, user_enable
			FROM ' . T_USERS . '
			WHERE user_id != 1 AND user_name = :user_name AND user_password = :password';
		if ($sth = $db->prepare($sql))
		{
			$sth->bindParam(':user_name', $username, PDO::PARAM_STR);
			$sth->bindParam(':password', $password, PDO::PARAM_STR);
			$sth->execute();
			$count = $sth->rowCount();
			$data = $sth->fetchObject();
			$sth->closeCursor();

			if($count == 0)
				return 0;
			if($data->user_enable == 'N')
				return -1;
			
			return (int)$data->user_id;
			
		} else trigger_error($sql, E_USER_ERROR);
	}
	

	/**
	 * 更新某用戶的最後登入時間
	 */
	private function _up_last_login($uid)
	{
		global $db;

		$sql = "UPDATE " . T_USERS . " SET last_login = " . time() . " WHERE user_id = " . intval($uid);
		if (!$db->query($sql))
			trigger_error($sql, E_USER_ERROR);
	}
	/**
	 * <p>刷新指定的sessions_keys</p>
	 * <p>這個方法僅提供類別內部使用</p>
	 */
	private function _up_keys_last_login($key_id)
	{
		global $db;
		if(preg_match($this->_guid_pattern, $key_id))
		{
			$sql = "UPDATE " . T_SESSIONS_KEYS . " SET last_login = " . time() . ", last_ip = '" . $this->_ip . "' WHERE key_id = '" . $key_id . "'";
			if (!$db->query($sql))
				trigger_error($sql, E_USER_ERROR);	
		}
	}	
	/**
	 * 依據當前SID傳回該sessions與users資料表的關聯陣列寫入到 $this->data
	 */
	private function _write_data()
	{
		global $db;
		if(preg_match($this->_guid_pattern, $this->_session_id))
		{
			$sql = "SELECT u.* , s.* FROM " . T_SESSIONS . " s, " . T_USERS . " u
			WHERE s.session_id = '" . $this->_session_id . "'
				AND s.session_user_id = u.user_id
				AND u.user_enable = 'Y'";
			if ($result = $db->query($sql))
			{
				$this->data = $result->fetch(PDO::FETCH_ASSOC);
				$result->closeCursor();
			} else trigger_error($sql, E_USER_ERROR);
		}
	}
	/**
	 * <p>這個方法用於檢驗COOKIE傳回的SID是否符合規定，並且檢查資料表中是否有存在這筆sid</p>
	 * <p>如果不符合會傳回空字串</p>
	 */
	private function _check_sid($check_sid)
	{
		global $db;

		if(!preg_match($this->_guid_pattern, $check_sid))
			return '';
		$sql = "SELECT COUNT(*) FROM " . T_SESSIONS . " WHERE session_id = '" . $check_sid . "'";
		if ($result = $db->query($sql))
		{
			$count = $result->fetchColumn(0);
			$result->closeCursor();
			if ($count != 1)
				return '';
		} else trigger_error($sql, E_USER_ERROR);
	
		return $check_sid;
	}
	
	/**
	 * <p>刷新指定的session</p>
	 * <p>這個方法僅提供類別內部使用</p>
	 */
	private function _updata_session()
	{
		global $db;
		if(preg_match($this->_guid_pattern, $this->_session_id))
		{
			$sql = "UPDATE " . T_SESSIONS . " SET
					session_last_time = " . $this->_time_now . ", session_ip = '" . $this->_ip . "', session_page = '" . $this->_script_name . "'
					WHERE session_id = '" . $this->_session_id . "'";
			if (!$db->query($sql))
				trigger_error($sql, E_USER_ERROR);
		}
	}
}



