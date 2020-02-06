<?php
namespace App\Helpers;

use App\Exceptions\InvalidUSPhoneFormatException;

class Phone {
	public static function format($phone) {
		$phone = preg_replace('/[^\d]/', '', $phone);

		if(strlen($phone) == 10) {
			$phone = '1'.$phone;
		}

        // Strip off local 1, convert exit code, format to domestic if possible
        // $phone = preg_replace('/^1(\d+)$/', '$1', $phone);
        $phone = preg_replace('/^011(\d+)$/', '+$1', $phone);

        $phone = preg_replace('/^(\d{1})(\d{3})(\d{3})(\d{4})$/', '+$1 $2-$3-$4', $phone);
        $phone = preg_replace('/^(\d{1})(\d{3})(\d{3})(\d{4})(\d+)$/', '+$1 $2-$3-$4 #$5', $phone);

        return $phone;
	}

	public static function format_is($phone) {
		$phone = preg_replace('/[^\d]/', '', $phone);
		
		if(strlen($phone) == 10) {
			$phone = '1'.$phone;
		}

		// Strip off local 1, convert exit code, format to domestic if possible
		// $phone = preg_replace('/^1(\d+)$/', '$1', $phone);
		$phone = preg_replace('/^011(\d+)$/', '$1', $phone);

		$phone = preg_replace('/^(\d{1})(\d{3})(\d{3})(\d{4})$/', '($2) $3-$4', $phone);
		$phone = preg_replace('/^(\d{1})(\d{3})(\d{3})(\d{4})(\d+)$/', '($2) $3-$4', $phone);

		return $phone;
	}

	public static function validate($phone) {
		$phone = self::clean($phone);
		return (strlen($phone) >= 10) ? $phone : null;
	}

	public static function clean($phone) {
		$phone = self::digits($phone);
		if(strlen($phone) == 10) {
			$phone = '1'.$phone;
		}
		return trim($phone);
	}
	
	public static function digits($phone) {
		return preg_replace('/\D+/', '', $phone);
	}

	public static function extract($value) {
		$phoneBit = \App\Helpers\Phone::digits($value);
		if(intval($phoneBit) && !preg_match('/[a-zA-Z]/',  $value))
			return $phoneBit;

		return null;
	}

	public static function get_country_code($phone) {
		if (stristr($phone,'+') && stristr($phone,' ')) {
			$phoneElements = explode(' ',$phone);
			return str_replace('+', '', $phoneElements[0]);
		}else{
			return 1;
		}
	}
	
	public static function areaCode($phone) {
		$phone = self::clean($phone);
		if(strlen($phone)==10):
			return substr($phone, 0, 3);
		elseif(strlen($phone)==11):
			return substr($phone, 1, 3);
		else:
			throw new InvalidUSPhoneFormatException();
		endif;
	}
}

