<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license Modified MIT License
 * @link https://blueimp.net/ajax/
 */

class CustomAJAXChat extends AJAXChat {
	
	/** wepeng(20160307) 
	* 强制所使用的编码
	*/
	function initDataBaseConnection() {
		// Create a new database object:
		$this->db = new AJAXChatDataBase(
			$this->_config['dbConnection']
		);
		// Use a new database connection if no existing is given:
		if(!$this->_config['dbConnection']['link']) {
			// Connect to the database server:
			$this->db->connect($this->_config['dbConnection']);
			if($this->db->error()) {
				echo $this->db->getError();
				die();
			}
			// Select the database:
			$this->db->select($this->_config['dbConnection']['name']);
			if($this->db->error()) {
				echo $this->db->getError();
				die();
			}
		}
		// Unset the dbConnection array for safety purposes:
		unset($this->_config['dbConnection']);			
		$charset = "utf8"; 
		$sql="SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary";
		$result = $this->db->sqlQuery($sql);
		// Stop if an error occurs:
		if($result->error()) {
			echo $result->getError();
			die();
		}
	}
	
	
	/** wepeng(20160303) 
	* 使用系统默认账户登录
	*/
	function login() {
		// Retrieve valid login user data (from request variables or session data):
		$userData = $this->getValidLoginUserData();
		//file_put_contents("login.txt", serialize($userData)."\r\n", FILE_APPEND);
		//print_r($userData);exit;
		/*
		Global $CFG;
		if(empty($CFG))
		{
			require(dirname(__FILE__)."/../../../config.php");
		}
		$userData = array();
		global $USER;
		$userData['userID'] = $USER->id;
		$userData['userName'] = fullname($USER, true);
		$userData['userRole'] = AJAX_CHAT_USER;
		$userData['channels'] = array(0,1);
		*/
		if(!$userData) {
			$this->addInfoMessage('errorInvalidUser');
			return false;
		}

		// If the chat is closed, only the admin may login:
		if(!$this->isChatOpen() && $userData['userRole'] != AJAX_CHAT_ADMIN) {
			$this->addInfoMessage('errorChatClosed');
			return false;
		}
		
		if(!$this->getConfig('allowGuestLogins') && $userData['userRole'] == AJAX_CHAT_GUEST) {
			return false;
		}

		// Check if userID or userName are already listed online:
		if($this->isUserOnline($userData['userID']) || $this->isUserNameInUse($userData['userName'])) {
			if($userData['userRole'] == AJAX_CHAT_USER || $userData['userRole'] == AJAX_CHAT_MODERATOR || $userData['userRole'] == AJAX_CHAT_ADMIN) {
				// Set the registered user inactive and remove the inactive users so the user can be logged in again:
				$this->setInactive($userData['userID'], $userData['userName']);
				$this->removeInactive();
			} else {
				$this->addInfoMessage('errorUserInUse');
				return false;
			}
		}
		
		// Check if user is banned:
		if($userData['userRole'] != AJAX_CHAT_ADMIN && $this->isUserBanned($userData['userName'], $userData['userID'], $_SERVER['REMOTE_ADDR'])) {
			$this->addInfoMessage('errorBanned');
			return false;
		}
		
		// Check if the max number of users is logged in (not affecting moderators or admins):
		if(!($userData['userRole'] == AJAX_CHAT_MODERATOR || $userData['userRole'] == AJAX_CHAT_ADMIN) && $this->isMaxUsersLoggedIn()) {
			$this->addInfoMessage('errorMaxUsersLoggedIn');
			return false;
		}

		// Use a new session id (if session has been started by the chat):
		$this->regenerateSessionID();

		// Log in:
		$this->setUserID($userData['userID']);
		$this->setUserName($userData['userName']);
		//echo $this->getUserName();exit;
		$this->setLoginUserName($userData['userName']);
		$this->setUserRole($userData['userRole']);
		$this->setLoggedIn(true);	
		$this->setLoginTimeStamp(time());

		// IP Security check variable:
		$this->setSessionIP($_SERVER['REMOTE_ADDR']);

		// The client authenticates to the socket server using a socketRegistrationID:
		if($this->getConfig('socketServerEnabled')) {
			$this->setSocketRegistrationID(
				md5(uniqid(rand(), true))
			);
		}

		// Add userID, userName and userRole to info messages:
		$this->addInfoMessage($this->getUserID(), 'userID');
		$this->addInfoMessage($this->getUserName(), 'userName');
		$this->addInfoMessage($this->getUserRole(), 'userRole');

		// Purge logs:
		if($this->getConfig('logsPurgeLogs')) {
			$this->purgeLogs();
		}

		return true;
	}
	
	/** wepeng(20160301)  不能改
	* 有channel必须先设置channel的session
	*
	*/
	function initialize() {
		// Initialize configuration settings:
		$this->initConfig();

		// Initialize the DataBase connection:
		$this->initDataBaseConnection();

		// Initialize request variables:
		$this->initRequestVars();
		
		// Initialize the chat session:
		$this->initSession();
		
		// Handle the browser request and send the response content:
		$this->handleRequest();
	}
	
	/** wepeng(20160301)  不能改
	* 消息初始化
	* 时间差
	*/
	function initMessageHandling() {
		// Don't handle messages if we are not in chat view:
		if($this->getView() != 'chat') {
			return;
		}

		// Check if we have been uninvited from a private or restricted channel:
		//file_put_contents('123.txt', 'initMessageHandling1:'.$this->getChannel().":\r\n", FILE_APPEND);
		if(!$this->validateChannel($this->getChannel())) {
			//file_put_contents('123.txt', 'initMessageHandling:'.$this->getChannel().":\r\n", FILE_APPEND);
			//file_put_contents('123.txt', 'initMessageHandling:'.serialize($this->_channels).":\r\n", FILE_APPEND);
			$this->switchChannel($this->getChannelNameFromChannelID($this->getConfig('defaultChannelID')));
			return;
		}
					
		if($this->getRequestVar('text') !== null) {
			$this->insertMessage($this->getRequestVar('text'));
		}
	}
	
	/** wepeng(20160301) 问题在这里
	* 获取系统的频道
	* 第一获取使用系统的默认配置，而后使用session
	* getChannelNameFromChannelID等两个函数未修改
	*/	
	function getCustomChannels() {
		//20160312
		$wepeng_channels = $this->getSessionVar('wepeng_channels');
		if(empty($wepeng_channels))
		{
			$this->setSessionVar('wepeng_channels', NULL);
			$channels = null;
			require(AJAX_CHAT_PATH.'lib/data/channels.php');
			$this->setSessionVar('wepeng_channels', array_flip($channels));
			//file_put_contents('123.txt', "getCustomChannels:new:\r\n", FILE_APPEND);
		}
		//file_put_contents("wepeng_channels.txt", serialize($this->getSessionVar('wepeng_channels')));
		return $this->getSessionVar('wepeng_channels');
	}
	
	/** wepeng(20160301) 问题在这里
	* 在session中添加频道
	*/	
	function addChannels($channelID, $channelName) {
		$channels = $this->getCustomChannels();
		$channels[$channelName] = $channelID;
		$this->setSessionVar('wepeng_channels', $channels);
		//更新类内容
		$this->_allChannels =  $channels;
		$this->_channels = $this->_allChannels;
		//file_put_contents('123.txt', 'addChannels:'.serialize($this->_channels).":\r\n", FILE_APPEND);
	}
	
	
	
	/** wepeng(20160301)
	* 切换频道
	* 
	*/	
	function switchChannel($channelName) {
		//file_put_contents('123.txt', $channelName."\r\n", FILE_APPEND);
		$channelID = $this->getChannelIDFromChannelName($channelName);
		//file_put_contents('getChannelIDFromChannelName.txt', $channelID);

		if($channelID !== null && $this->getChannel() == $channelID) {
			// User is already in the given channel, return:
			return;
		}

		//file_put_contents('123.txt', $channelID);
		// Check if we have a valid channel:
		if(!$this->validateChannel($channelID)) {
			// Invalid channel:
			$text = '/error InvalidChannelName switchChannel'.$channelName;
			$this->insertChatBotMessage(
				$this->getPrivateMessageID(),
				$text
			);
			return;
		}

		$oldChannel = $this->getChannel();
		//file_put_contents('setChannel.txt', $channelID);
		$this->setChannel($channelID);
		$this->updateOnlineList();
		//exit;

		// Channel leave message
		$text = '/channelLeave '.$this->getUserName();
		$this->insertChatBotMessage(
			$oldChannel,
			$text,
			null,
			1
		);
		

		// Channel enter message
		$text = '/channelEnter '.$this->getUserName();
		$this->insertChatBotMessage(
			$this->getChannel(),
			$text,
			null,
			1
		);

		$this->addInfoMessage($channelName, 'channelSwitch');
		$this->addInfoMessage($channelID, 'channelID');
		$this->_requestVars['lastID'] = 0;
		
		//wepeng 20160301 切换频道后删除频道消息未读状态
		$sql = "SELECT * FROM  `ajax_chat_no_message` WHERE  `toUserID` =".$this->db->makeSafe($this->getUserID())." AND  `channelName` LIKE  ".$this->db->makeSafe($this->getChannelName())."";
		$result = $this->db->sqlQuery($sql);
		// Stop if an error occurs:
		if($result->error()) {
			echo $result->getError();
			die();
		}
		$row = $result->fetch();
		if(!empty($row))
		{
			$sql = "DELETE FROM `ajax_chat_no_message` WHERE  `toUserID` =".$this->db->makeSafe($this->getUserID())." AND  `channelName` LIKE  ".$this->db->makeSafe($this->getChannelName())."";
			$result = $this->db->sqlQuery($sql);
			// Stop if an error occurs:
			if($result->error()) {
				echo $result->getError();
				die();
			}
		}
	}
	
	/** wepeng(20160301)
	* 验证channelID是否可以访问
	* 
	* 20160303 在认证之前执行getChannelNameFromChannelID
	*/	
	function validateChannel($channelID) {
		if($channelID === null) {
			return false;
		}
		//return true;
		
		//20160303
		$this->getChannelNameFromChannelID($channelID);
		
		// Return true for normal channels the user has acces to:
		if(in_array($channelID, $this->getChannels())) {
			return true;
		}
		//这里有时会出现$channelID和$channelName为空的情况，不知道为什么，去掉return true;可以测试。
		//file_put_contents("123.txt", $channelID."\r\n".serialize($this->getChannels())."\r\n\r\n", FILE_APPEND);
		// Return true if the user is allowed to join his own private channel:
		if($channelID == $this->getPrivateChannelID() && $this->isAllowedToCreatePrivateChannel()) {
			return true;
		}
		// Return true if the user has been invited to a restricted or private channel:
		if(in_array($channelID, $this->getInvitations())) {
			return true;	
		}
		// No valid channel, return false:
		return false;
	}
	
	
	
	/** wepeng(20160301)
	*
	* 修改在线列表为未读消息列表
	* 
	*/
	
	function getChatViewOnlineUsersXML($channelIDs) {
		// Get the online users for the given channels:
		$sql = "SELECT * FROM  `ajax_chat_no_message` WHERE  `toUserID` ='".$this->getUserID()."'";
		$result = $this->db->sqlQuery($sql);
		// Stop if an error occurs:
		if($result->error()) {
			echo $result->getError();
			die();
		}
		$xml = '<users>';
		while($onlineUserData = $result->fetch()) {
			$xml .= '<user';
			$xml .= ' userID="'.$onlineUserData['userID'].'"';
			$xml .= ' userRole="1"';
			$xml .= ' channelID="0"';
			$xml .= '>';
			$xml .= '<![CDATA['.$this->encodeSpecialChars($onlineUserData['userName']).']]>';
			$xml .= '<![CDATA['.$this->encodeSpecialChars($onlineUserData['channelName']).']]>';
			$xml .= '</user>';
		}
		$xml .= '</users>';	
		return $xml;
	}
	
	/** wepeng(20160308)
	* 插入消息
	* 
	* 消息未读则插入
	*/
	function insertCustomMessage($userID, $userName, $userRole, $channelID, $text, $ip=null, $mode=0) {
		// The $mode parameter is used for socket updates:
		// 0 = normal messages
		// 1 = channel messages (e.g. login/logout, channel enter/leave, kick)
		// 2 = messages with online user updates (nick)
		
					//wepeng 20160229  有新消息则插入数据库 
					//默认频道设置为自己对自己说话
					if(strpos($this->getChannelName(), "_") !== FALSE)
					{
						//file_put_contents("insertParsedMessage.txt", $text);
						$userID = $this->getUserID();
						$toUserIDs = explode("_", $this->getChannelName());
						$toUserID = NULL;
						foreach($toUserIDs as $value)
						{
							if($value != $userID)
							{
								$toUserID = $value;
							}
						}
						//自己发给自己不处理
						if($toUserID === NULL)
						{
							break;
						}
						$userName = $this->getUserName();
						//判断对方是否在线并在同一channel中
						$sql = "SELECT * FROM  `ajax_chat_online` WHERE  `userID` =".$this->db->makeSafe($toUserID)." AND  `channel` =".$this->db->makeSafe($this->getChannel())."";
						$result = $this->db->sqlQuery($sql);
						// Stop if an error occurs:
						if($result->error()) {
							echo $result->getError();
							die();
						}
						//20160308
						$row = $result->fetch();
						if(empty($row))
						{
							//不在同一个channel中则插入数据库
							$channelName = $this->getChannelName();
							$sql = "INSERT INTO `ajax_chat_no_message` (`userID`, `toUserID`, `userName`, `channelName`) VALUES ('$userID', '$toUserID', '$userName ', '$channelName')";
							$result = $this->db->sqlQuery($sql);
						}
					}
					//end

					
		$ip = $ip ? $ip : $_SERVER['REMOTE_ADDR'];
		
		$sql = 'INSERT INTO '.$this->getDataBaseTable('messages').'(
								userID,
								userName,
								userRole,
								channel,
								dateTime,
								ip,
								text
							)
				VALUES (
					'.$this->db->makeSafe($userID).',
					'.$this->db->makeSafe($userName).',
					'.$this->db->makeSafe($userRole).',
					'.$this->db->makeSafe($channelID).',
					NOW(),
					'.$this->db->makeSafe($this->ipToStorageFormat($ip)).',
					'.$this->db->makeSafe($text).'
				);';

		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);
		
		// Stop if an error occurs:
		if($result->error()) {
			echo $result->getError();
			die();
		}
		
		if($this->getConfig('socketServerEnabled')) {
			$this->sendSocketMessage(
				$this->getSocketBroadcastMessage(
					$this->db->getLastInsertedID(),
					time(),
					$userID,
					$userName,
					$userRole,
					$channelID,
					$text,
					$mode
				)
			);	
		}
	}
	
	/** wepeng(20160304)
	* 通过channelID获取channelName
	* 
	* 从`ajax_chat_channel`数据库中查询
	*/
	function getChannelNameFromChannelID($channelID) {
		//20160304 先从session中判断ID是否存在,如果存在则返回名字
		//file_put_contents("11.txt", $channelID."\r\n".serialize($this->getCustomChannels())."\r\n");
		
		$key = array_search($channelID, $this->getCustomChannels());
		if($key !== False)
		{
			return $key;
		}
		
		
		//向数据库查询不存在则重新分配
		$sql = "SELECT `name` FROM  `ajax_chat_channel` WHERE  `id` =  ".$this->db->makeSafe($channelID)."";
		//file_put_contents("123.txt", $sql, FILE_APPEND);
		$result = $this->db->sqlQuery($sql);
		// Stop if an error occurs:
		if($result->error()) {
			echo $result->getError();
			die();
		}
		$row = $result->fetch();
		if(!empty($row))
		{
			//20160301 更新对象列表
			$this->_allChannels[$row['name']] = $channelID;
			$this->_channels = $this->_allChannels;
			$this->addChannels($channelID, $row['name']);
			return $row['name'];
		} else {
			//返回公共聊天室
			return $this->getConfig('defaultChannelName');
		}
	}
	
	/** wepeng(20160304)
	* 通过channelName获取channelID
	* 
	* 从`ajax_chat_channel`数据库中查询
	*/
	function getChannelIDFromChannelName($channelName) {
		$channelName = $this->trimChannelName($channelName, $this->getConfig('contentEncoding'));
		if(!$channelName)
			return null;
		
		//20160304 先从session中判断ID是否存在,如果存在则返回名字
		$channel = $this->getCustomChannels();
		if(isset($channel[$channelName]))
		{
			return $channel[$channelName];
		}
		
		//向数据库查询不存在则重新分配
		$sql = "SELECT `id` FROM  `ajax_chat_channel` WHERE  `name` =  ".$this->db->makeSafe($channelName)."";
		$result = $this->db->sqlQuery($sql);
		// Stop if an error occurs:
		if($result->error()) {
			echo $result->getError();
			die();
		}
		$row = $result->fetch();
		if(empty($row))
		{
			$sql = "INSERT INTO`ajax_chat_channel` (`id`, `name`) VALUES (NULL, ".$this->db->makeSafe($channelName).")";
			$this->db->sqlQuery($sql);
			// Stop if an error occurs:
			if($result->error()) {
				echo $result->getError();
				die();
			}
			$channelID = $this->db->getLastInsertedID();
		} else {
			$channelID = $row['id'];
		}
		
		$this->addChannels($channelID, $channelName);
		return $channelID;
	}

	// Returns an associative array containing userName, userID and userRole
	// Returns null if login is invalid
	/** wepeng(20160304)
	* 修改为直接返回用户数据
	* 
	*/
	function getValidLoginUserData() {
		//return $this->getCustomUsers()[0];
		
		$customUsers = $this->getCustomUsers();
		
		if($this->getRequestVar('password')) {
			// Check if we have a valid registered user:

			$userName = $this->getRequestVar('userName');
			$userName = $this->convertEncoding($userName, $this->getConfig('contentEncoding'), $this->getConfig('sourceEncoding'));

			$password = $this->getRequestVar('password');
			$password = $this->convertEncoding($password, $this->getConfig('contentEncoding'), $this->getConfig('sourceEncoding'));

			foreach($customUsers as $key=>$value) {
				if(($value['userName'] == $userName) && ($value['password'] == $password)) {
					$userData = array();
					$userData['userID'] = $key;
					$userData['userName'] = $this->trimUserName($value['userName']);
					$userData['userRole'] = $value['userRole'];
					return $userData;
				}
			}
			
			return null;
		} else {
			// Guest users:
			return $this->getGuestUser();
		}
	}
	
	/** wepeng(20160307)
	* 修改为直接返回用户数据
	* 
	*/
	function getGuestUser() {
		/*
		if(!$this->getConfig('allowGuestLogins'))
			return null;

		if($this->getConfig('allowGuestUserName')) {
			$maxLength =	$this->getConfig('userNameMaxLength')
							- $this->stringLength($this->getConfig('guestUserPrefix'))
							- $this->stringLength($this->getConfig('guestUserSuffix'));

			// Trim guest userName:
			$userName = $this->trimString($this->getRequestVar('userName'), null, $maxLength, true, true);

			// If given userName is invalid, create one:
			if(!$userName) {
				$userName = $this->createGuestUserName();
			} else {
				// Add the guest users prefix and suffix to the given userName:
				$userName = $this->getConfig('guestUserPrefix').$userName.$this->getConfig('guestUserSuffix');	
			}
		} else {
			$userName = $this->createGuestUserName();
		}

		$userData = array();
		$userData['userID'] = $this->createGuestUserID();
		$userData['userName'] = $userName;
		$userData['userRole'] = AJAX_CHAT_GUEST;
		return $userData;		
		*/
		//wepeng 20160308 获取用户名和ID在cookie中 
		/*
		Global $CFG;
		if(empty($CFG))
		{
			require(dirname(__FILE__)."/../../../config.php");
		}*/
		// List containing the registered chat users:
		//$users = null;
		//require(AJAX_CHAT_PATH.'lib/data/users.php');
		//global $USER;
		$userData = array();
		//$userData['userID'] = $USER->id;
		$userData['userID'] = $_COOKIE['wepeng_userID'];
		//$userName = $this->convertEncoding(fullname($USER, true), $this->getConfig('contentEncoding'), $this->getConfig('sourceEncoding'));
		//$userData['userName'] = fullname($USER, true);
		$userData['userName'] = $_COOKIE['wepeng_userName'];;
		$userData['userRole'] = AJAX_CHAT_USER;
		//$userData['channels'] = array(0,1);
		//print_r($userData);exit;
		return $userData;
	}

	// Store the channels the current user has access to
	// Make sure channel names don't contain any whitespace
	/** wepeng(20160301)
	* 获取可以访问的频道
	* 
	* 修改为所有的频道都可以访问
	*/
	function &getChannels() {
		if($this->_channels === null) {
			$this->_channels = array();
			
			$customUsers = $this->getCustomUsers();
			
			// Get the channels, the user has access to:
			//20160307 wepeng $validChannels = $customUsers[$this->getUserID()]['channels'];出错
			/*
			if($this->getUserRole() == AJAX_CHAT_GUEST) {
				$validChannels = $customUsers[0]['channels'];
			} else {
				$validChannels = $customUsers[$this->getUserID()]['channels'];
			}*/
			$validChannels = $customUsers[0]['channels'];
			
			// Add the valid channels to the channel list (the defaultChannelID is always valid):
			foreach($this->getAllChannels() as $key=>$value) {
				$this->_channels[$key] = $value;
			}
		}
		return $this->_channels;
	}

	// Store all existing channels
	// Make sure channel names don't contain any whitespace
	function &getAllChannels() {
		if($this->_allChannels === null) {
			// Get all existing channels:
			$customChannels = $this->getCustomChannels();
			
			$defaultChannelFound = false;
			
			foreach($customChannels as $name=>$id) {
				$this->_allChannels[$this->trimChannelName($name)] = $id;
				if($id == $this->getConfig('defaultChannelID')) {
					$defaultChannelFound = true;
				}
			}
			
			if(!$defaultChannelFound) {
				// Add the default channel as first array element to the channel list
				// First remove it in case it appeard under a different ID
				unset($this->_allChannels[$this->getConfig('defaultChannelName')]);
				$this->_allChannels = array_merge(
					array(
						$this->trimChannelName($this->getConfig('defaultChannelName'))=>$this->getConfig('defaultChannelID')
					),
					$this->_allChannels
				);
			}
		}
		return $this->_allChannels;
	}

	/** wepeng(20160306)
	* 修改为直接返回moodle登录的用户数据
	* 
	*/
	function &getCustomUsers() {
		//echo dirname(__FILE__)."/../configh.php";exit;
		//require(dirname(__FILE__)."/../../../configh.php");
		/*
		Global $CFG;
		if(empty($CFG))
		{
			require(dirname(__FILE__)."/../../../config.php");
		}
		// List containing the registered chat users:
		$users = null;
		//require(AJAX_CHAT_PATH.'lib/data/users.php');
		global $USER;
		$userData[0]['userID'] = $USER->id;
		//$userName = $this->convertEncoding(fullname($USER, true), $this->getConfig('contentEncoding'), $this->getConfig('sourceEncoding'));
		$userData[0]['userName'] = fullname($USER, true);
		$userData[0]['userRole'] = AJAX_CHAT_USER;
		$userData[0]['channels'] = array(0,1);
		//print_r($userData);exit;
		return $userData;
		*/
		// List containing the custom channels:
		// List containing the registered chat users:
		$users = null;
		require(AJAX_CHAT_PATH.'lib/data/users.php');
		return $users;
	}

}