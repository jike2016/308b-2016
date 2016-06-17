/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license Modified MIT License
 * @link https://blueimp.net/ajax/
 */

// Ajax Chat config parameters:
var ajaxChatConfig = {

	// The channelID of the channel to enter on login (the loginChannelName is used if set to null):
	loginChannelID: null,
	// The channelName of the channel to enter on login (the default channel is used if set to null):
	loginChannelName: null,	
	
	// The time in ms between update calls to retrieve new chat messages:
	timerRate: 2000,
	
	// The URL to retrieve the XML chat messages (must at least contain one parameter):
	ajaxURL: './?ajax=true',
	// The base URL of the chat directory, used to retrieve media files (images, sound files, etc.):
	baseURL: './',

	// A regular expression for allowed source URL's for media content (e.g. images displayed inline);
	regExpMediaUrl: '^((http)|(https)):\\/\\/',
	
	// If set to false the chat update is delayed until the event defined in ajaxChat.setStartChatHandler():
	startChatOnLoad: true,
	
	// Defines the IDs of DOM nodes accessed by the chat:
	domIDs: {
		// The ID of the chat messages list:
		chatList: 'chatList',
		// The ID of the online users list:
		onlineList: 'onlineList',
		// The ID of the message text input field:
		inputField: 'inputField',
		// The ID of the message text length counter:
		messageLengthCounter: 'messageLengthCounter',
		// The ID of the channel selection:
		channelSelection: 'channelSelection',
		// The ID of the style selection:
		styleSelection: 'styleSelection',
		// The ID of the emoticons container:
		emoticonsContainer: 'emoticonsContainer',
		// The ID of the color codes container:
		colorCodesContainer: 'colorCodesContainer',
		// The ID of the flash interface container:
		flashInterfaceContainer: 'flashInterfaceContainer'
	},

	// Defines the settings which can be modified by users:
	settings: {
		// Defines if BBCode tags are replaced with the associated HTML code tags:
		bbCode: true,
		// Defines if image BBCode is replaced with the associated image HTML code:
		bbCodeImages: true,
		// Defines if color BBCode is replaced with the associated color HTML code:
		bbCodeColors: true,
		// Defines if hyperlinks are made clickable:
		hyperLinks: true,
		// Defines if line breaks are enabled:
		lineBreaks: true,
		// Defines if emoticon codes are replaced with their associated images:
		emoticons: true,
	
		// Defines if the focus is automatically set to the input field on chat load or channel switch:
		autoFocus: true,
		// Defines if the chat list scrolls automatically to display the latest messages:
		autoScroll: true,	
		// The maximum count of messages displayed in the chat list (will be ignored if set to 0):
		maxMessages: 0,
		
		// Defines if long words are wrapped to avoid vertical scrolling:
		wordWrap: true,
		// Defines the maximum length before a word gets wrapped: 
		maxWordLength: 32,
		
		// Defines the format of the date and time displayed for each chat message:
		dateFormat: '(%H:%i:%s)',
		
		// Defines if font colors persist without the need to assign them to each message:
		persistFontColor: false,	
		// The default font color, uses the page default font color if set to null:
		fontColor: null,
		
		// Defines if sounds are played:
		audio: true,
		// Defines the sound volume (0.0 = mute, 1.0 = max):
		audioVolume: 1.0,

		// Defines the sound that is played when normal messages are reveived:
		soundReceive: 'sound_1',
		// Defines the sound that is played on sending normal messages:
		soundSend: 'sound_2',
		// Defines the sound that is played on channel enter or login:
		soundEnter: 'sound_3',
		// Defines the sound that is played on channel leave or logout:
		soundLeave: 'sound_4',
		// Defines the sound that is played on chatBot messages:
		soundChatBot: 'sound_5',
		// Defines the sound that is played on error messages:
		soundError: 'sound_6',
		
		// Defines if the document title blinks on new messages:
		blink: true,
		// Defines the blink interval in ms:
		blinkInterval: 500,
		// Defines the number of blink intervals:
		blinkIntervalNumber: 10
	},
	
	// Defines a list of settings which are not to be stored in a session cookie:
	nonPersistentSettings: [],

	// Defines the list of allowed BBCodes:
	bbCodeTags:[
		'b',
		'i',
		'u',
		'quote',
		'code',
		'color',
		'url',
		'img'
	],
	
	// Defines the list of allowed color codes:
	colorCodes: [
		'gray',
		'silver',
		'white',	
		'yellow',
		'orange',
		'red',
		'fuchsia',
		'purple',
		'navy',
		'blue',
		'aqua',
		'teal',
		'green',
		'lime',
		'olive',
		'maroon',
		'black'
	],
	
	// Defines the list of allowed emoticon codes:
	emoticonCodes: [
		':微笑:',
		':撇嘴:',
		':色:',
		':发呆:',
		':流泪:',
		':害羞:',
		':闭嘴:',
		':睡:',
		':大哭:',
		':尴尬:',
		':发怒:',
		':调皮:',
		':呲牙:',
		':惊讶:',
		':难过:',
		':冷汗:',
		':抓狂:',
		':吐:',
		':偷笑:',
		':可爱:',
		':白眼:',
		':傲慢:',
		':饥饿:',
		':困:',
		':惊恐:',
		':流汗:',
		':憨笑:',
		':大兵:',
		':奋斗:',
		':咒骂:',
		':疑问:',
		':嘘:',
		':晕:',
		':折磨:',
		':衰:',
		':敲打:',
		':再见:',
		':擦汗:',
		':抠鼻:',
		':糗大了:',
		':坏笑:',
		':左哼哼:',
		':右哼哼:',
		':哈欠:',
		':鄙视:',
		':快哭了:',
		':委屈:',
		':阴险:',
		':亲亲:',
		':吓:',
		':可怜:',
		':拥抱:',
		':月亮:',
		':太阳:',
		':炸弹:',
		':骷髅:',
		':菜刀:',
		':猪头:',
		':西瓜:',
		':咖啡:',
		':饭:',
		':爱心:',
		':强:',
		':弱:',
		':握手:',
		':胜利:',
		':抱拳:',
		':勾引:',
		':OK:',
		':NO:',
		':玫瑰:',
		':凋谢:',
		':红唇:',
		':飞吻:',
		':示爱:'	
 	],
	
 	// Defines the list of emoticon files associated with the emoticon codes:
	emoticonFiles: [
		'1.gif',
		'2.gif',
		'3.gif',
		'4.gif',
		'5.gif',
		'6.gif',
		'7.gif',
		'8.gif',
		'9.gif',
		'10.gif',
		'11.gif',
		'12.gif',
		'13.gif',
		'14.gif',
		'15.gif',
		'16.gif',
		'17.gif',
		'18.gif',
		'19.gif',
		'20.gif',
		'21.gif',
		'22.gif',
		'23.gif',
		'24.gif',
		'25.gif',
		'26.gif',
		'27.gif',
		'28.gif',
		'29.gif',
		'30.gif',
		'31.gif',
		'32.gif',
		'33.gif',
		'34.gif',
		'35.gif',
		'36.gif',
		'37.gif',
		'38.gif',
		'39.gif',
		'40.gif',
		'41.gif',
		'42.gif',
		'43.gif',
		'44.gif',
		'45.gif',
		'46.gif',
		'47.gif',
		'48.gif',
		'49.gif',
		'50.gif',
		'51.gif',
		'52.gif',
		'53.gif',
		'54.gif',
		'55.gif',
		'56.gif',
		'57.gif',
		'58.gif',
		'59.gif',
		'60.gif',
		'61.gif',
		'62.gif',
		'63.gif',
		'64.gif',
		'65.gif',
		'66.gif',
		'67.gif',
		'68.gif',
		'69.gif',
		'70.gif',
		'71.gif',
		'72.gif',
		'73.gif',
		'74.gif',
		'75.gif'
	],
	
	// Defines the available sounds loaded on chat start:
	soundFiles: {
		sound_1: 'sound_1.mp3',
		sound_2: 'sound_2.mp3',
		sound_3: 'sound_3.mp3',
		sound_4: 'sound_4.mp3',
		sound_5: 'sound_5.mp3',
		sound_6: 'sound_6.mp3'
	},
	
	
	// Once users have been logged in, the following values are overridden by those in config.php.
	// You should set these to be the same as the ones in config.php to avoid confusion.
	
	// Session identification, used for style and setting cookies:
	sessionName: 'ajax_chat',

	// The time in days until the style and setting cookies expire:
	cookieExpiration: 365,
	// The path of the cookies, '/' allows to read the cookies from all directories:
	cookiePath: '/',
	// The domain of the cookies, defaults to the hostname of the server if set to null:
	cookieDomain: null,
	// If enabled, cookies must be sent over secure (SSL/TLS encrypted) connections:
	cookieSecure: null,
	
	// The name of the chat bot:
	chatBotName: 'ChatBot',
	// The userID of the chat bot:
	chatBotID: 2147483647,

	// Allow/Disallow registered users to delete their own messages:
	allowUserMessageDelete: true,
	
	// Minutes until a user is declared inactive (last status update) - the minimum is 2 minutes:
	inactiveTimeout: 2,

	// UserID plus this value are private channels (this is also the max userID and max channelID):
	privateChannelDiff: 500000000,
	// UserID plus this value are used for private messages:
	privateMessageDiff: 1000000000,

	// Defines if login/logout and channel enter/leave are displayed:
	showChannelMessages: true,

	// Max messageText length:
	messageTextMaxLength: 1040,
	
	// Defines if the socket server is enabled:
	socketServerEnabled: false,
	// Defines the hostname of the socket server used to connect from client side:
	socketServerHost: 'localhost',
	// Defines the port of the socket server:
	socketServerPort: 1935,
	// This ID can be used to distinguish between different chat installations using the same socket server:
	socketServerChatID: 0

}