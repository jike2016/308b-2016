VideoEasy Filter
=========================================
VideoEasy is a filter that replaces links to media files, with  html5 players. Primarliy intended for html 5 video, it will also work for audio, youtube or rss links. The Video Easy filter uses templates to support multiple players, and allows the user to add new players or customize existing ones, all from the Video Easy filter settings pages. By default players are already defined, and can be used as is, for:
Video.js, JPlayer, JW Player, Flowplayer and MediaElement.js

But preset templates are available for other players, and you are encouraged to use the existing players and presets as examples, and make your own templates.

Installation
=========================================
If you are uploading videoeasy, first expand the zip file and upload the videoeasy folder into:
[PATH TO MOODLE]/filters.

Then visit your Moodle server's Site Administration -> Notifications page. Moodle will guide you through the installation. On the final page of the installation you will be able to register templates. Since there are 15 template slots available, and each has a lot of fields, You should just scroll to the bottom and press "save." After this each template has its own settings page and it is much easier to work with the settings that way.

After installing you will need to enable the videoeasy filter. You can enable the videoeasy filter when you visit:
Site Administration / plugins / filters / manage filters

Finally you will need to associate a player/template with each of the file extensions, and check the file extensions that VideoEasy should handle.
Do this at: Site Administration / plugins / filters / VideoEasy / General Settings

(NB Sublime Video and JW Player, require you to register on their sites, and you will be given a license code. You will need to enter that into the template for those players. See "Templates" in this document.)

Configuration
=========================================
Configure the site wide settings  and define/edit templates at 
Site Administration / plugins / filters / Video Easy

On the general settings page you need to check the filetypes you wish the Video Easy filter to handle at the top of the settings page, and select the player template(drop down list) that will handle that file extension.

Many player templates will require JQuery. We used to load this as required. And you still can. (The checkbox for that is still on each template page.) But that is now unchecked by default. Please do not use it. It will be removed in a subsequent version. Instead you should use a theme that loads JQuery already (Essential, BCU are two), or add a call to load JQuery to the Moodle site header.
To add that, go to: Site Administration -> Appearance -> Additional HTML (within HEAD) ,and add:
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>

NB In Moodle 2.9 VideoJS player must be set to use AMD. By default it now is, but if you are upgrading from an earlier version of VideoJS it won't be. Look for "Load via AMD" on the template settings page for VideoJS.

NB You should TURN OFF file handling for any extensions you select here in the Moodle Multi Media Plugins filter, and the PoodLL filter if they are installed.
Multi Media Plugins filter settings can be found at:
Site Administration / appearance / media embedding

PoodLL filter settings can be found at:
Site Administration / plugins / filters / PoodLL

Local Configuration
========================================
One of the strengths of Video Easy is that it makes use of the under utilized Moodle feature that allows you to configure filters at the course and at the activity level. Using this, for example, it is possible to display videos in a particular page using a different template/player to that used elsewhere. This would make it possible to make a page with 100 videos embedded, behave differently to a page with just a single video.

NB There seem to be conflicts (jquery?) that prevent some player types loading on the same screen at the same time. JWPlayer tried to turn all video tags into JWPlayers and the non AMD players/templates (e.g youtube lightbox and mediaelement.js) an conflict with each other. So when you use local filter settings, be cautious with labels and blocks since these create the possibility of different player types being on the screen at the same time.

The rest of this document gets a bit technical and I don't want to scare non-techies off. So from here its not strictly necessary to read on.  

Templates
=========================================
There are fifteen templates available to use. The first six are ready made, though they can be altered.They are: Video.js, Sublime Video, JW Player, Flowplayer, MediaElement.js, and Youtube lightbox.

JW Player requires that you register with their site to get a personal javascript link. So you will need to do that first then enter it in the requires_js field of the template before you can use them.

Each template has several fields, but only the name/key field is required:
1) required javascript url : The url of the JS file the html5 player requires.
2) required css url : The url of the CSS file the html5 player requires.
3) requires jquery : True or False. You should set this to false. JQuery should now be loaded earlier than VideoEasy can. Either by your theme or via Moodle's additional html settings.
4) load via AMD: Since Moodle 2.9 certain libraries are loaded using Require.js. If the player library supports it, you will need to set this to true. Currently only VideoJS seems to need this.
5) template : The html that goes on the page. Often this is just a div , with a unique id. Sometimes it is html5 video tags.
6) load script : Any script which the player runs to load an individual player, usually with the unique id of a container div
7) defaults : Custom variables you may use in the template or load script, or default values for existing variables (ie width and height).
8) custom css: CSS that you need on the page, that can be edited here on the settings page.
9) upload css: It is possible to upload a CSS file for inclusion on the page. This is probably in the case that the file is not available online to be be simply linked to. 
10) upload js: It is possible to upload a JS file for inclusion on the page. This is probably in the case that the file is not available online to be be simply linked to. 

Presets
=====================================
Each template's page contains a drop down with a number of "preset" templates. (template templates ..I guess). The list of presets will grow hopefully as people submit them to me, or I dream them up. Using these you can quickly make new templates, or use as a starting point for your own template. The current presets are:
Video.js, Sublime Video, JW Player, Flowplayer, MediaElement.js,Youtube Lightbox, YouTube(standard),Multi Source Audio, Multi Source Video, JW Player RSS, and SoundManager 2 

In order to keep VideoEasy small, there are no actual JS players bundled. Flowplayer, Sublime Video etc are all included on the page via CDN hosting sources. In some cases, notably SoundManager 2, it will work better if you have those players installed on your own web server. SoundManager2 has flash components, which are sensitive to crossdomain hosting issues. 

The Video Easy Variables
=====================================

Variables are used to replace placeholders in the template and load scripts. A placeholder for a variable looks like this: @@VARIABLE@@ (variable name surrounded by @@ marks.)

These variables are generated by Video Easy after parsing the media link on the Moodle page. You can define your own in the defaults section if you wish. 
NB Video Easy supports the ?d=[width]x[height] notation that Moodle's multi media plugins filter uses, for all extensions, but not for Youtube links. But since almost nobody ever uses it, in most cases you will want to specify a width and height in the defaults section for the template. 

AUTOMIME = video file mime type determined by file extension.
FILENAME = filename of video
AUTOPNGFILENAME = the video filename but with a png extension
AUTOJPGFILENAME = the video filename but with a jpg extension
VIDEOURL = the url of the video
URLSTUB = the url of the video minus the file extension. 
AUTOPOSTERURLJPG = the full video url but with a jpg extension
AUTOPOSTERURLPNG = the full video url but with a png extension
DEFAULTPOSTERURL = url to a default poster image. VideoEasy ships with  bland grey image. But you can upload your own default poster image on the Video Easy general settings page.
TITLE = the video title (from linked text)
AUTOID = an auto generated id to use in the container div or elsewhere
CSSLINK = used internally to load a CSS file if needed.
PLAYER = the type of player (videojs, flowplayer ...etc)
WIDTH = the width of video
HEIGHT = the height of video
FILEEXT = The file extension of the video file

Note that while the template replacement is a simple swap out of the placeholder text, the loader script replacement is a little different. The loader script replacement will remove surrounding quotes as well as the placeholder, and put a JS variable in their place.
 eg
 template: <video id="@@AUTOID@@" 
 becomes: <video id="123456"
 
 loader script: player{ id: "@@AUTOID@@"
 becomes: player{ id: opts['AUTOID']
 
And a final caution, Video Easy generates a loader script from the template (if required) but this will be cached by Moodle in most cases. Thats a good thing too. But it means you will need to run Moodle "purge all caches" after making changes to anything on the Video Easy filter settings page.

What happened to Sublime Video?
==========
They got bought out, and everyone had to transition off. So I removed the preset, because it was meaningless.

What is AMD?
==========
AMD is a javascript loading system. It helps manage the order in which dependant libraries and loaded, and to prevent jquery conflicts. Moodle started to use it in version 2.9, so any AMD settings are ignored in earlier Moodle versions. Even if you don't use a 3rd party javascript library (eg a JQuery Plugin), AMD is still a good choice and it will load JQuery for you. If you do use a 3rd party library your choice to use AMD, will depend on whether the library supports it or not. Its hard to know that, but errors related to Require JS in the browser console, that are not preceded by other template related JS errors, are a sign that you have chosen the wrong AMD option.

The Future
===========
The next important step for VideoEasy is to offer up a form for each template with a field for each of the variables defined there. Given the correct permissions a user could then customize the player behaviour without the CSS and JS complexity. And the settings could be made available at the course and activity level, so that users can have, for example, different sized players on different pages. 

Enjoy

Justin Hunt
poodllsupport@gmail.com





