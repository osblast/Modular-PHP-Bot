<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2013
 */

	class ChanList {

		var $chan_list;

		function __construct()
		{
			$this->chan_list = new ArrayList();
		}

		function Count()
		{
			return($this->chan_list->Length());
		}

		function Item($index)
		{
			return($this->chan_list->Item($index));
		}

		function AddChannel($channel)
		{
			$this->chan_list->Add($channel);
		}

		function RemoveChannel($channel)
		{
			$this->chan_list->Remove($channel);
		}

		function findChannelByName($channel_name)
		{
			for($tzIter=0; $tzIter<$this->chan_list->Length(); $tzIter++)
			{

				$chan = $this->chan_list->Item($tzIter);

				if(strtoupper($chan->Name) == strtoupper($channel_name))
					return($chan);
			}

			return null;
		}

		function queryUserByNickname($nickname)
		{
			// goes through each channel's member list and finds which channels the user is in.  It returns
			// all of them in an array.
			$arrMemberInstances = array();
			$total_found_index = -1;

			for($tzIter=0; $tzIter < $this->chan_list->Length(); $tzIter++)
			{
				$channelObj = $this->chan_list->Item($tzIter);

				$cMember = $channelObj->MemberList->findMemberByNickname($nickname);

				if($cMember != null)
				{
					$total_found_index++;
					$cMember->attachedChannel = $channelObj->Name;
					$arrMemberInstances[$total_found_index] = $cMember;
				}
			}

			if($total_found_index < 0)
				return(null);

			return($arrMemberInstances);
		}
	}


?>