<?php


	class Color {

		/*	mIRC Colours:
			0 white
			1 black
			2 blue (navy)
			3 green
			4 red
			5 brown (maroon)
			6 purple
			7 orange (olive)
			8 yellow
			9 light green (lime)
			10 teal (a green/blue cyan)
			11 light cyan (cyan) (aqua)
			12 light blue (royal)
			13 pink (light purple) (fuchsia)
			14 grey
			15 light grey (silver)
			*/

		public static $mIRC = array("white" 	=> 	"00",
									"black"		=>	"01",
									"blue"		=>	"02",
									"green"		=>	"03",
									"red"		=>	"04",
									"brown"		=>	"05",
									"purple"	=>	"06",
									"orange"	=>	"07",
									"yellow"	=>	"08",
									"lgreen"	=>	"09",
									"teal"		=>	"010",
									"cyan"		=>	"011",
									"lblue"		=>	"012",
									"pink"		=>	"013",
									"grey"		=>	"014",
									"lgrey"		=>	"015",
									);

		public static function mIRC($color)
		{
			if(Color::$mIRC[strtolower($color)] != null)
				return(Color::$mIRC[strtolower($color)]);
		}


	}


?>