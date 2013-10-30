<?php

	include_once("ArrayList.php");

	class ChannelMember
	{
		var $Nickname;
		var $IsOpped;
		var $IsVoiced;
		var $UnknownFlags; /* any special ircd flags specific to an ircd, eg &, %, etc
		   					  this list should be built on as new flags are found.
						   */

		var $Host;
		var $UserID;

		var $attachedChannel;

		function __construct($nickname, $opped=false, $voiced=false)
		{
			$this->Nickname = $nickname;
			$this->IsOpped = $opped;
			$this->IsVoiced = $voiced;
		}
	}

	class MemberList
	{
	  	var $mem_list;

		function __construct()
		{
			$this->mem_list = new ArrayList();
		}

		function Count()
		{
			return($this->mem_list->Length());
		}

		function Item($index)
		{
			return($this->mem_list->Item($index));
		}

		function AddMember($member)
		{
			$this->mem_list->Add($member);
		}

		function RemoveMember($member)
		{
			$this->mem_list->Remove($member);
		}

		function findMemberByNickname($nickname)
		{

			for($tzIter=0; $tzIter<$this->mem_list->Length(); $tzIter++)
			{
				$member = $this->mem_list->Item($tzIter);

				if(strtoupper($member->Nickname) == strtoupper($nickname))
					return($member);
			}

			return null;
		}

	}

	class Channel
	{

		var $Name;
		var $MemberList;
		var $Topic;
		var $TopicSetBy;
		var $TopicSetTime;

		function __construct($name)
		{
			$this->MemberList = new MemberList();
			$this->Name = $name;
		}

	}


?>