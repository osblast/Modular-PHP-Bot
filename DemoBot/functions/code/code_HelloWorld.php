<?php

	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel,"Color Test: ".TextFormatter::Color(Color::mIRC("red"), Color::mIRC("white"))."Red Background, White Foreground".TextFormatter::EndColor()));

//	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel,"Style Test: ".TextFormatter::Bold()." Bold ".TextFormatter::Bold().TextFormatter::Underline()." Underline ".TextFormatter::Underline().TextFormatter::Italic()." Italic ".TextFormatter::Italic().TextFormatter::Inverse()." Inverse ".TextFormatter::Inverse()));

//	$bot->socketHandler->send($bot->ircHandler->PRIVMSG($bot->botConfig->DebugChannel,"Alignment Test:   left: [".TextFormatter::Pad("String", 15, "l")."]  right: [".TextFormatter::Pad("String", 15, "r")."] center: [".TextFormatter::Pad("String", 15, "c")."]"));


?>