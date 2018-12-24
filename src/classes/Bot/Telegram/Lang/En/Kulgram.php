<?php

namespace Bot\Telegram\Lang\En;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 5.0.0
 * @since 2.0.1
 * @package \Bot\Telegram\Lang\Id
 */
class Kulgram
{
	public static $l = [
		"intro" => "Usage: /kulgram [command] [option]\nCommands:\n help Show this message\n init Initialize a kulgram session\n start Start a kulgram session\n stop  Stop a kulgram session\n cancel Cancel a kulgram session",
 		"unknown" => "Unknown command `:cmd`. Use /kulgram help to show all commands.",
 		"init.usage" =>  "Usage: /kulgram init --title <title> --author <author>\n\n --title  Set kulgram title\n --author Set kulgram author\n\nUsage Example: /kulgram init --title \"How to make a good program\" --author \"Ammar Faizi\"",
		"init.error_no_title" => "Error: title parameter requeired\n\nUsage: /kulgram init --title <title> --author <author>\n\n --title  Set kulgram title\n --author Set kulgram author\n\nUsage Example: /kulgram init --title \"How to make a good program\" --author \"Ammar Faizi\"",
		"init.error_no_author" => "Error: author parameter requeired\n\nUsage: /kulgram init --title <title> --author <author>\n\n --title  Set kulgram title\n --author Set kulgram author\n\nUsage Example: /kulgram init --title \"How to make a good program\" --author \"Ammar Faizi\"",
		"init.ok" => "A kulgram session has been initialized!",
		"init.on.running" => "An error occured: There is a running session in this group, please stop the current session first!",
		"init.on.idle" => "An error occured: There is an idle session in this group, please cancel the current session first!",
		"start.ok" => "The kulgram session record has been started, kindly to start the kulgram.",
		"start.on.off" => "An error occured: The kulgram session record has not been initialized yet, please init a kulgram session first!",
		"start.on.running" => "An error occured: The kulgram session record is being started, please stop the current session first!",
		"cancel.ok" => "The kulgram session has been cancelled!",
		"cancel.on.running" => "An error occured: The kulgram session cannot be cancelled because it is being ran, you can stop the session instead.",
		"cancel.on.off" => "An error occured: There is no session record to be cancelled.",
		"stop.on.off" => "An error occured: There is no running session to be stopped.",
		"stop.on.idle" => "An error occured: There is no running session to be stopped. You may cancel the idle session by using /kulgram cancel.",
		"stop.p1" => "Executing data query...",
		"stop.p2" => "Stopping system record...",
		"stop.p3" => "Building PDF data...",
		"stop.ok" => "OK",
		"unknown_error" => "An error occured: Unknown error :fl"
	];
}
