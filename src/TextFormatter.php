<?php

	class TextFormatter {

		public static function Bold()
		{
			return("\002");
		}

		public static function Underline()
		{
			return("\037");
		}

		public static function Clear()
		{
			return("\017");
		}

		public static function Tab()
		{
			return("\011");
		}

		public static function Italic()
		{
			return("\035");
		}

		public static function Inverse()
		{
			return("\026");
		}

		public static function Color($background, $foreground)
		{
			//Color Character is 3.  background,foreground
			return("\003".$foreground.",".$background);
		}

		public static function EndColor()
		{
			return("\003");
		}

		public static function Pad($str, $width, $align=null, $char=null)
		{
			if($char==null)
				$char = chr(32);

			if($align==null)
				$align="l"; /* default to left align */

			$ret_str = "";

			if(strlen($str) > $width)
				$str = substr($str, 0, $width);  // cut off excess length of string

			switch(strtolower($align))
			{
				case "l": /* left align */
					$ret_str = $str.str_repeat($char, $width-strlen($str));
					break;

				case "r": /* right align */
					$ret_str = str_repeat($char, $width-strlen($str)).$str;
					break;

				case "c": /* center align */

					// String: Capsicum, Length = 8, Pad = 20, Align = Center

					// First thing we would do, is modulus to get the right pad value
					// then we get the left value by subtracting the right value

					// string len =6  "String"
					// pad length =15
					//
					// width minus strlen = 15 - 6 = 9
					// right pad length =  9 % 2 =  remainder of 9/2  =   4.5
					// left pad length = 9 - right pad length = 4.5
					//
					// [    ]

					$width_minus_strlen = ($width - strlen($str));
					$right_pad_len = ceil($width_minus_strlen / 2);
					$left_pad_len = floor($width_minus_strlen - $right_pad_len);
					$ret_str = str_repeat($char, $left_pad_len).$str.str_repeat($char, $right_pad_len);
					break;
			}


			return($ret_str);
		}

	}


?>