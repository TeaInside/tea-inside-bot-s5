<?php

namespace Bot\Telegram\Lang\En;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @since 2.0.1
 * @package \Bot\Telegram\Lang\Id
 */
class Help
{
	public static $l = [
		"private" => 
"<b>Global Commands:</b>
/sh <code>[command arg...]</code>\tExecute linux shell command.
/tr &lt;from> &lt;to> &lt;string&gt\tTranslate a text.
/fb &lt;fb_username&gt;|&lt;fb_userid&gt;\tGet facebook profile photo.
/debug\tShow telegram JSON debug message.
/whatanime\t(Reply to photo only) Search anime name by screenshot.

<b>Group Commands:</b>
/welcome <code>&lt;text_html&gt;</code>\t Set welcome message.
/delete_welcome\t Delete welcome welcome message.
/promote\tReply to someone's message to promote him to be an admin.
/kulgram\tRecord a kulgram.

<b>Virtualizor Commands:</b>
[Starts with]
<code>&lt;?php</code>\tPHP script.
<code>&lt;?py</code>\tPython 3 script.
<code>&lt;?py3</code>\tPython 3 script.
<code>&lt;?py2</code>\tPython 2 script.
<code>&lt;?c</code>\tC script.
<code>&lt;?c++</code>\tC++ script.
<code>&lt;?cpp</code>\tC++ script.",

		"group" => "Help command can only be used in private!"
	];
}
