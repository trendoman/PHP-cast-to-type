<?php

/**
 * Class to easily cast variables to a specific type
 *
 */
class CastToType {
	
	function __construct( $value, $type, $allow_empty = true, $implode_array = false, $explode_string = false ) {

		// Have the expected variables been passed ?
		if ( isset( $value ) === false || isset( $type ) === false ) {
			return null;
		}
	
		$type = strtolower( trim( $type ) );
		$valid_types = array( 'bool' => 1, 'boolean' => 1, 'int' => 1, 'integer' => 1, 'float' => 1, 'num' => 1, 'string' => 1, 'array' => 1, 'object' => 1, );
		//$value = trim( $value );
	
		// Check if the typing passed is valid, if not return NULL
		if ( !isset( $valid_types[$type] ) ) {
			return null;
		}
	
		switch ( $type ) {
			case 'bool':
			case 'boolean':
				return self::_bool( $value );
				break;
	
			case 'integer':
			case 'int':
				return self::_int( $value );
				break;
	
			case 'float':
				return self::_float( $value );
				break;
	
			case 'num':
				if ( is_numeric( $value ) ) {
					$value = ( ( (float) $value != (int) $value ) ? (float) $value : (int) $value );
				}
				else {
					$value = null;
				}
				return $value;
				break;
	
			case 'string':
				return self::_string( $value, $implode_array, $allow_empty );
				break;
	
			case 'array':
				return self::_array( $value, $allow_empty );
				break;
	
			case 'object':
				return self::_object( $value );
				break;

			case 'null':
			default:
				return null;
				break;
		}
	}

	function CastToType( $value, $type, $allow_empty = false, $implode_array = false, $explode_string = false ) {
		$this->__construct( $value, $type, $allow_empty = false, $implode_array = false, $explode_string = false );
	}

	

	
	static function _bool( $value ) {
		$true  = array(
			'1',
			'true', 'True', 'TRUE',
			'y', 'Y',
			'yes', 'Yes', 'YES',
			'on', 'On', 'On',
	
		);
		$false = array(
			'0',
			'false', 'False', 'FALSE',
			'n', 'N',
			'no', 'No', 'NO',
			'off', 'Off', 'OFF',
		);
	
		if ( is_bool( $value ) ) {
			return $value;
		}
		else if ( ( is_int( $value ) || is_float( $value ) ) && ( $value === 0 || $value === 1 ) ) {
			return (bool) $value;
		}
		else if ( is_string( $value ) ) {
			$value = trim( $value );
			if ( in_array( $value, $true, true ) ) {
				return true;
			}
			else if ( in_array( $value, $false, true ) ) {
				return false;
			}
			else {
				return null;
			}
		}
		else if ( is_object( $value ) && get_class( $value ) === 'SplBool' ) {
			if( $value == true ) {
				return true;
			}
			else if ( $value == false ) {
				return false;
			}
			else {
				return null;
			}
		}
		else {
			return null;
		}
	}
	
	static function int( $value ) {
	
		if ( is_int( $value ) ) {
			return $value;
		}
		else if ( is_float( $value ) ) {
			if ( (int) $value == $value ) {
				return ( int) $value;
			}
			else {
				return null;
			}
		}
		else if ( is_string( $value ) ) {
			$value = trim( $value );
			if ( $value === '' ) {
				return null;
			}
			else if ( ctype_digit( $value ) ) {
				return (int) $value;
			}
			else if ( strpos( $value, '-' ) === 0 && ctype_digit( substr( $value, 1 ) ) ) {
				return (int) $value ;
			}
			else {
				return null;
			}
		}
		else if ( is_object( $value ) && get_class( $value ) === 'SplInt' ) {
			if( (int) $value == $value ) {
				return (int) $value;
			}
			else {
				return null;
			}
		}
		else {
			return null;
		}
	}
	
	static function _float( $value ) {
		if( is_float( $value ) ) {
			return $value;
		}
		else if ( is_object( $value ) && get_class( $value ) === 'SplFloat' ) {
			if( (float) $value == $value ) {
				return (float) $value;
			}
			else {
				return null;
			}
		}
		else if ( is_numeric( $value ) && ( floatval( $value ) == $value ) ) {
			return floatval( $value );
		}
		else {
			return null;
		}
	}
	
	
	static function _string( $value, $implode_array = false, $allow_empty = true ) {
		if ( is_string( $value ) && ( $value !== '' || $allow_empty === true ) ) {
			return $value;
		}
		else if ( is_int( $value ) ) {
			return strval( $value );
		}
		else if ( $implode_array === true && ( is_array( $value ) && count( $value ) > 0 ) ) {
			return self::mul_dim_implode( $value, ' *{', '}* ', true,	' [', '] => ', $level = 0 );
		}
		else if ( is_object( $value ) && get_class( $value ) === 'SplString' ) {
			if( (string) $value == $value ) {
				return (string) $value;
			}
			else {
				return null;
			}
		}
		else {
			return null;
		}
	}
	
	
	static function _array( $value, $allow_empty = true ) {
		if ( is_array( $value ) !== true ) {
			$value = (array) $value;
		}

		if ( count( $value ) > 0 || $allow_empty === true ) {
			return $value;
		}
		else {
			return null;
		}
	}
	
	static function _object( $value ) {
		if ( is_object( $value ) !== true ) {
			$value = (object) $value;
		}
		return $value;
	}
	
	
	// for completeness
	static function _null( $value ) {
		return null;
	}
	
	
	
	
	/*
	http://nl2.php.net/manual/nl/function.explode.php
	britz_pm at hotmail dot com
	19-Oct-2005 10:23
	PLEASE NOTE I HAD TO BREAK SOME LINES CAUSE OF WORDWRAP() WAS NOT HAPPY :(
	
	Well i thought of making some versions of explode/implode
	functions with can do any depth of multi-dimensional arrays
	with or without keeping the keys
	
	make/change the defaults as you need them
	as for error checking i did not add any because would probably
	make it take longer to run add em if you please
	*/
	static function mul_dim_implode( $array, $start_glue, $end_glue, $with_keys = false, $start_key_glue = null, $end_key_glue = null, $level = 0 ) {
	
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) === true ) {
				$value = self::mul_dim_implode( $value, $start_glue, $end_glue, $with_keys, $start_key_glue, $end_key_glue, ( $level + 1 ) );
			}
	
			if ( isset( $string ) === false ) {
				$string = ( $with_keys === false ) ? $value : ( $key . $start_key_glue . $level . $end_key_glue . $value );
			}
			else {
				$string .= ( $with_keys === false ) ? ( $start_glue . $level . $end_glue . $value ) : ( $start_glue . $level . $end_glue . $key . $start_key_glue . $level . $end_key_glue . $value );
			}
		}
		return $string;
	}
	
	static function mul_dim_explode( $string, $start_glue, $end_glue, $with_keys = false, $start_key_glue = null, $end_key_glue = null, $level = 0 ) {
	
		if ( strstr( $string, $start_glue . $level . $end_glue ) ) {
			$temp_array = explode( $start_glue . $level . $end_glue, $string );
			foreach ( $temp_array as $value ) {
				if ( $with_keys === true ) {
					$temp = explode( $start_key_glue . $level . $end_key_glue, $value );
					$array[$temp[0]] = self::mul_dim_explode( $temp[1], $start_glue, $end_glue, $with_keys, $start_key_glue, $end_key_glue, ( $level + 1 ) );
				}
				else {
					$array[] = self::mul_dim_explode( $value, $start_glue, $end_glue, $with_keys, $start_key_glue, $end_key_glue, ( $level + 1 ) );
				}
			}
		}
		else {
			return (array) $string;
		}
		return $array;
	}
}

?>