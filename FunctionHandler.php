<?php

	include_once("ArrayList.php");

	class FunctionHandler {

		var $functionList;

		function __construct()
		{
			$this->functionList = new ArrayList();
		}

		function Reload()
		{
			$arrFunctions = array();
			$funcIndex = -1;

			$func_path = "functions";

			$fcdir_file_list = scandir($func_path);

			for($tzIter=0;$tzIter<count($fcdir_file_list); $tzIter++)
			{
				if(is_file($func_path."/".$fcdir_file_list[$tzIter]))
				{
					$f_spl_data = explode(".",$fcdir_file_list[$tzIter]);

					if(count($f_spl_data) > 0)
					{
						$f_ext = $f_spl_data[1];

						if(strtoupper($f_ext) === "PHP" && strtoupper($fcdir_file_list[$tzIter]) != "INDEX.PHP")
						{
							include_once($func_path."/".$fcdir_file_list[$tzIter]);

							$funcId = "Function_".$f_spl_data[0];

							if(class_exists($funcId))
							{
								$cmdClass = $funcId;

								$bf = new $cmdClass();

								$old_instance = $this->findByCommand($bf->FunctionIdentifier);
								$this->functionList->Remove($old_instance);

								$this->functionList->Add($bf);

								$funcIndex++;
								$arrFunctions[$funcIndex] = $funcId;

								echo("Added new function: ".$bf->FunctionIdentifier."\r\n");
							}
							else
							{
								echo("Error adding command: ".$funcId."\r\n");
							}

						}
					}
				}
			}

			return($arrFunctions);

		}

		function AddFunctionByCommand($command)
		{
			if(!is_file("functions/".$command.".php"))
				return;

			include_once("functions/".$command.".php");

			if(class_exists("Function_".$command))
			{
				$cmdClass = "Function_".$command;

				$bf = new $cmdClass();
				$this->functionList->Add($bf);

				echo("Added new function: ".$bf->FunctionIdentifier."\r\n");
			}
			else
			{
				echo("Error adding command: ".$command."\r\n");
			}

		}

		function findByCommand($command)
		{

			for($tzIter=0; $tzIter<$this->functionList->Length(); $tzIter++)
			{
				$func = $this->functionList->Item($tzIter);

				if(strtoupper($func->FunctionIdentifier) == strtoupper($command))
					return($func);
			}

			return null;
		}

	}


?>