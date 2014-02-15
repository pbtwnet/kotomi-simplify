<?php
class config
{
	/**
	 * <p>這個屬性是陣列，在這個類別實體化時(建構元時產生)</p>
	 * <p>會將組態資料表放入這個陣列(使用關聯陣列) 存取組態使用以下方式$conf->data['名稱']</p>
	 * @var array()
	 */
	public $data = array();
	
	private $_key = array();
	private $_count = 0;
	/**
	 * 建構元，執行初始化的組態查詢
	 */
	function __construct()
	{
		global $db;
	
		$sql = 'SELECT config_name, config_value FROM ' . T_CONFIG;
		if ($result = $db->query($sql))
		{
			$this->_count = 0;
			while ($row = $result->fetchObject())
			{
				$this->_key[] = $row->config_name;
				$this->data[$row->config_name] = $row->config_value;
				$this->_count = $this->_count + 1;
			}
			$result->closeCursor();
		} else trigger_error($sql, E_USER_ERROR);
	}
}
