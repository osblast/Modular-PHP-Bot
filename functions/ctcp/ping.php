<?php

//$data = :anthonym!anthonym@irc-9FA3E7D1.sbr800.nsw.optusnet.com.au PRIVMSG PHPBot :PINGG 1382980450

$ping_data = explode(" ", $data);

$users_ctime = $ping_data[4];
$users_ctime = substr($users_ctime, 0, strlen($users_ctime)-1);

$bot->socketHandler->send($this->ircHandler->NOTICE($from_nick, chr(1)."PING ".$users_ctime.chr(1)));

?>