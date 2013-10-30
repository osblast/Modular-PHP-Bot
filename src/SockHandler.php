<?php


	class SockHandler {

		var $socket;
		var $socketoptions;
		var $socketcontext;

		function __construct()
		{
			$this->socketoptions = array();
			$this->socketcontext = stream_context_create($this->socketoptions);
		}

		function connect($hostname, $port)
		{

			$this->socket = stream_socket_client($hostname.":".$port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $this->socketcontext);
			while(!is_resource($this->socket));
			return(TRUE);
		}

		function send($data)
		{
			//echo("<- ".$data."\r\n");

			if(is_resource($this->socket))
			{
				fwrite($this->socket, $data. "\r\n");
				return(TRUE);
			}
			else
			{
				return(FALSE);
			}
		}

		function read()
		{
			if(is_resource($this->socket))
			{
				return fgets($this->socket);
			}
			else
			{
				return FALSE;
			}
		}

	}

?>