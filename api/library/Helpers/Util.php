<?php

namespace Helpers;
use \SendGrid\Mail\Attachment as Attachment;

class Util
{

	public static function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public static function downloadFile($originDir, $filename, $mimetype){
		if (!file_exists($originDir))
			return Error::getErrorArray(Error::HTTP_INTERNAL_SERVER_ERROR, "Arquivo não encontrado.");
		header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers 
        header('Content-Type: '.$mimetype);
        header('Content-Disposition: attachment; filename="'. $filename . '";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($originDir));
        readfile($originDir);
        exit;
	}

	public static function createAttachment($dir, $mimeType, $filename)
	{
		$attachment = new Attachment();
        $attachment->setContent(base64_encode(file_get_contents($dir)));
        $attachment->setType($mimeType);
        $attachment->setFilename($filename);
        $attachment->setDisposition("attachment");
		return $attachment;
	}

	public static function clearString($string)
	{
		$string = trim($string);
		$string = str_replace("\'", '`', $string);
		$string = str_replace("'", '`', $string);
		$string = str_replace("--", '', $string);

		return $string;
	}


	public static function clearFileName($name)
	{
		$name = self::clearString(str_replace(' ', '-', $name));

		return strtolower(preg_replace("/[^\w\-\.]/", '', self::normalize($name)));
	}


	public static function normalize(string $string)
	{
		$table = array('Š' => 'S',
					   'š' => 's',
					   'Đ' => 'Dj',
					   'đ' => 'dj',
					   'Ž' => 'Z',
					   'ž' => 'z',
					   'Č' => 'C',
					   'č' => 'c',
					   'Ć' => 'C',
					   'ć' => 'c',
					   'À' => 'A',
					   'Á' => 'A',
					   'Â' => 'A',
					   'Ã' => 'A',
					   'Ä' => 'A',
					   'Å' => 'A',
					   'Æ' => 'A',
					   'Ç' => 'C',
					   'È' => 'E',
					   'É' => 'E',
					   'Ê' => 'E',
					   'Ë' => 'E',
					   'Ì' => 'I',
					   'Í' => 'I',
					   'Î' => 'I',
					   'Ï' => 'I',
					   'Ñ' => 'N',
					   'Ò' => 'O',
					   'Ó' => 'O',
					   'Ô' => 'O',
					   'Õ' => 'O',
					   'Ö' => 'O',
					   'Ø' => 'O',
					   'Ù' => 'U',
					   'Ú' => 'U',
					   'Û' => 'U',
					   'Ü' => 'U',
					   'Ý' => 'Y',
					   'Þ' => 'B',
					   'ß' => 'Ss',
					   'à' => 'a',
					   'á' => 'a',
					   'â' => 'a',
					   'ã' => 'a',
					   'ä' => 'a',
					   'å' => 'a',
					   'æ' => 'a',
					   'ç' => 'c',
					   'è' => 'e',
					   'é' => 'e',
					   'ê' => 'e',
					   'ë' => 'e',
					   'ì' => 'i',
					   'í' => 'i',
					   'î' => 'i',
					   'ï' => 'i',
					   'ð' => 'o',
					   'ñ' => 'n',
					   'ò' => 'o',
					   'ó' => 'o',
					   'ô' => 'o',
					   'õ' => 'o',
					   'ö' => 'o',
					   'ø' => 'o',
					   'ù' => 'u',
					   'ú' => 'u',
					   'û' => 'u',
					   'ý' => 'y',
					   'þ' => 'b',
					   'ÿ' => 'y',
					   'Ŕ' => 'R',
					   'ŕ' => 'r',);

		return strtr($string, $table);
	}


	public static function textilize($text)
	{
		//$parser = new \Netcarver\Textile\Parser();
		//return $parser->textileThis( $text );
		return $text;
	}


	public static function getNiceTime($date)
	{
		if (empty($date)) {
			return "No date provided";
		}

		$periods   = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths   = array("60", "60", "24", "7", "4.35", "12", "10");
		$now       = time();
		$unix_date = strtotime($date);

		if (empty($unix_date)) {
			return "Bad date";
		}

		if ($now == $unix_date) {
			return "right now";
		} else {
			if ($now > $unix_date) {
				$difference = $now - $unix_date;
				$tense      = "ago";
			} else {
				$difference = $unix_date - $now;
				$tense      = "from now";
			}
		}

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if ($difference != 1) {
			$periods[$j] .= "s";
		}

		return "$difference $periods[$j] {$tense}";
	}


	public static function singularToPlural($word)
	{
		$singular_plural_array = self::singularAndPlural();

		if (isset($singular_plural_array[$word])) {
			return $singular_plural_array[$word];
		}

		return $word.'s';
	}


	private static function singularAndPlural()
	{
		return array('activity'               => 'activities',
					 'accessory'              => 'accessories',
					 'address'                => 'addresses',
					 'body'                   => 'bodies',
					 'category'               => 'categories',
					 'campaign_category'      => 'campaign_categories',
					 'city'                   => 'cities',
					 'person'                 => 'people',
					 'quiz'                   => 'quizzes',
					 'Quiz'                   => 'Quizzes',
					 'vehicle_accessory'      => 'vehicle_accessories',
					 'VehicleAccessory'       => 'VehicleAccessories',
					 'freepass'               => 'freepasses',
					 'match'                  => 'matches',
					 'footstats_match'        => 'footstats_matches',
					 'person_footstats_match' => 'person_footstats_matches',
					 'status'                 => 'statuses',
					 'order_status'           => 'order_statuses',);
	}


	public static function camelcaseToUnderscore($input)
	{
		$input = explode('\\', $input);
		$input = $input[count($input) - 1];

		return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $input)), '_');
	}


	public static function words($string, $words_returned, $dots = true, $strip_tags = true)
	{
		$string = ($strip_tags ? strip_tags($string) : $string);
		$retval = $string;
		$array  = explode(" ", $string);

		if (count($array) <= $words_returned) {
			$retval = $string;
		} else {
			array_splice($array, $words_returned);
			$retval = implode(" ", $array).($dots ? "..." : "");
		}

		return trim($retval);
	}


	public static function letters($string, $words_returned, $dots = true, $strip_tags = true)
	{
		$string = trim(($strip_tags ? strip_tags($string) : $string));
		$string = str_replace("\n", "", $string);
		$string = str_replace("\r", "", $string);
		$retval = (strlen($string) > $words_returned ? substr($string, 0, $words_returned) : $string);
		if (strlen($string) > $words_returned && $dots) {
			if ($last_space = strrpos($retval, " ")) {
				$retval = substr($retval, 0, $last_space);
			}

			$retval .= "...";
		}

		return $retval;
	}


	public static function parseDatabaseFormat($date)
	{
		#
		# 30/01/2008 => 2008-01-30 (não altera as horas, se informado)
		#

		if (!self::isDateFormat($date)) {
			return $date;
		}

		$hora = substr($date, 10, 9);
		$date = substr($date, 0, 10);

		return (implode('-', array_reverse(explode('/', $date)))).$hora;
	}


	public static function isDateFormat($date)
	{
		if (!is_integer(substr($date, 2, 1)) && !is_integer(substr($date, 5, 1)) && is_numeric(substr($date, 6, 4))) {
			return true;
		} else {
			return false;
		}
	}


	public static function parseDateFormat($date, $show_time = false)
	{
		#
		# 2008-01-30 => 30/01/2008 (não altera as horas, se habilitado)
		#

		if (self::isDateFormat($date)) {
			return $date;
		}

		$hora = substr($date, 10, 6);
		$date = substr($date, 0, 10);

		return (implode('/', array_reverse(explode('-', $date)))).($show_time ? $hora : '');
	}


	public static function dateDiff($startDate, $endDate)
	{
		$startArry  = date_parse($startDate);
		$endArry    = date_parse($endDate);
		$start_date = gregoriantojd($startArry["month"], $startArry["day"], $startArry["year"]);
		$end_date   = gregoriantojd($endArry["month"], $endArry["day"], $endArry["year"]);

		return round(($end_date - $start_date), 0);
	}


	public static function timeAt($startTime)
	{
		$start_date  = new DateTime($startTime);
		$since_start = $start_date->diff(new DateTime(date('Y-m-d H:i:s')));

		$phrase = '';

		if ($since_start->y) {
			$phrase = 'Publicado '.$since_start->y.($since_start->y > 1 ? ' anos' : ' ano').' atrás';
		} else {
			if ($since_start->m) {
				$phrase = 'Publicado '.$since_start->m.($since_start->m > 1 ? ' meses' : ' mês').' atrás';
			} else {
				if ($since_start->d) {
					if (($since_start->d == 7) || ($since_start->d > 7 && $since_start->d < 14)) {
						$phrase = 'Publicado semana passada';
					} else {
						if ($since_start->d >= 14 && $since_start->d < 21) {
							$phrase = 'Publicado duas semanas atrás';
						} else {
							if ($since_start->d >= 21 && $since_start->d < 31) {
								$phrase = 'Publicado três semanas atrás';
							} else {
								$phrase = 'Publicado '.($since_start->d > 1 ? $since_start->d.' dias atrás' : 'ontem');
							}
						}
					}
				} else {
					if ($since_start->h) {
						$phrase = 'Publicado '.$since_start->h.($since_start->h > 1 ? ' horas' : ' hora').' atrás';
					} else {
						if ($since_start->i > 30 && $since_start->i < 59) {
							$phrase = 'Publicado quase uma hora atrás';
						} else {
							if ($since_start->i) {
								$phrase = 'Publicado '.$since_start->i.($since_start->i > 1 ? ' minutos' : ' minuto').' atrás';
							} else {
								if ($since_start->s) {
									$phrase = 'Publicado '.$since_start->s.($since_start->s > 1 ? ' segundos' : ' segundo').' atrás';
								}
							}
						}
					}
				}
			}
		}

		return $phrase;
	}


	public static function getLongDate($date)
	{
		$date = strtotime($date);
		$date = date('d', $date).' de '.util::getMonthName(date('m', $date)).', '.date('Y', $date);

		return $date;
	}


	public static function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = \DateTime::createFromFormat($format, $date);

		return $d && $d->format($format) == $date;
	}


	public static function getMonthName($m)
	{
		switch ($m) {
			case '01':
				return 'janeiro';
				break;
			case '02':
				return 'fevereiro';
				break;
			case '03':
				return 'março';
				break;
			case '04':
				return 'abril';
				break;
			case '05':
				return 'maio';
				break;
			case '06':
				return 'junho';
				break;
			case '07':
				return 'julho';
				break;
			case '08':
				return 'agosto';
				break;
			case '09':
				return 'setembro';
				break;
			case '10':
				return 'outubro';
				break;
			case '11':
				return 'novembro';
				break;
			case '12':
				return 'dezembro';
				break;
		}
	}


	public static function formatBirthday($birthday)
	{
		$birthday = str_replace('\\', '', $birthday);
		$birthday = explode('/', $birthday);
		$birthday = sprintf("%02s", $birthday[2]).'-'.sprintf("%02s", $birthday[0]).'-'.sprintf("%02s", $birthday[1]);

		return $birthday;
	}


	public static function pluralToSingular($word)
	{
		$singular_plural_array = self::singularAndPlural();

		if (array_search($word, $singular_plural_array)) {
			return array_search($word, $singular_plural_array);
		}

		return rtrim($word, 's');
	}


	public static function underscoreToCamelcase($input)
	{
		$input = explode('_', strtolower($input));

		foreach ($input as &$value) {
			$value = ucwords($value);
		}

		return implode('', $input);
	}


	public static function formatCurrency($number)
	{
		return 'R$ '.number_format($number, 2, ',', '.');
	}


	public static function unformatCurrency($currency)
	{
		$out = str_replace('R$ ', '', $currency);
		$out = str_replace('.', '', $out);
		$out = str_replace(',', '.', $out);

		return $out;
	}


	public static function imageBase64Resize($image_path)
	{
		$fn    = $image_path;
		$size  = getimagesize($fn);
		$ratio = $size[0] / $size[1]; // width/height

		if ($ratio > 1) {
			$width  = 640;
			$height = 640 / $ratio;
		} else {
			$width  = 480 * $ratio;
			$height = 480;
		}

		$src = imagecreatefromstring(file_get_contents($fn));
		$dst = imagecreatetruecolor($width, $height);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);

		ob_start();
		imagejpeg($dst);
		$base64 = ob_get_contents();
		ob_end_clean();

		imagedestroy($src);
		imagedestroy($dst);

		return 'data:image/jpg;base64,'.base64_encode($base64);
	}


	public static function hashPassword($plain_password)
	{
		//return (strlen($plain_password) == 32 ? $plain_password : md5($plain_password));
		return password_hash($plain_password, PASSWORD_DEFAULT);
	}


	public static function verifyPassword($plain_password, $hash)
	{
		return password_verify($plain_password, $hash);
	}


	function dateAdd($date, $days)
	{
		$date = explode('-', $date);

		return date('Y-m-d', mktime(0, 0, 0, $date[1], $date[2] + $days, $date[0]));
	}


	function decodeSum($elements, $sum)
	{
		#
		# GET MAXIMUM SUM POSSIBLE
		#
		$max = 0;
		$j   = 1;

		for ($i = 1; $i < $elements; $i++) {
			$max += $j;
			$j   = $j * 2;
		}

		$max += $j;

		#
		# CHECK VALUES
		#
		$results = array();

		for ($i = $elements; $i > 0; $i--) {
			if ($j <= $sum) {
				$results[$i - 1] = true;
				$sum             -= $j;
			} else {
				$results[$i - 1] = false;
			}

			$j = $j / 2;
		}

		return $results;
	}


	/**
	 * Delete properties from class that you won't return
	 *
	 * @param \stdClass $obj
	 * @param array     $attr
	 *
	 * @return \stdClass | array
	 */
	public static function unsetObjAattr(&$obj, array $attr)
	{
		if (is_object($obj)) {
			foreach (get_object_vars($obj) as $key => $value) {
				if (!in_array($key, $attr)) {
					unset($obj->$key);
				}
			}
		} else if (is_array($obj)) {
			foreach ($obj as $key => $value) {
				if (!is_object($value)) {
					if (!in_array($key, $attr)) {
						unset($obj[$key]);
					}
				} else {
					foreach (get_object_vars($value) as $field => $item) {
						if (!in_array($field, $attr)) {
							unset($value->$field);
						}
					}
				}
			}
		}
	}


	public static function getBrowser()
	{
		$u_agent  = $_SERVER['HTTP_USER_AGENT'];
		$bname    = 'Unknown';
		$platform = 'Unknown';
		$version  = "";
		// First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		} else if (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		} else if (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}
		// Next get the name of the useragent yes seperately and for good reason
		if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
			$bname = 'Internet Explorer';
			$ub    = "MSIE";
		} else if (preg_match('/Firefox/i', $u_agent)) {
			$bname = 'Mozilla Firefox';
			$ub    = "Firefox";
		} else if (preg_match('/Chrome/i', $u_agent)) {
			$bname = 'Google Chrome';
			$ub    = "Chrome";
		} else if (preg_match('/Safari/i', $u_agent)) {
			$bname = 'Apple Safari';
			$ub    = "Safari";
		} else if (preg_match('/Opera/i', $u_agent)) {
			$bname = 'Opera';
			$ub    = "Opera";
		} else if (preg_match('/Netscape/i', $u_agent)) {
			$bname = 'Netscape';
			$ub    = "Netscape";
		}
		// finally get the correct version number
		$known   = array('Version', $ub, 'other');
		$pattern = '#(?<browser>'.join('|', $known).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
				$version = $matches['version'][0];
			} else {
				$version = $matches['version'][1];
			}
		} else {
			$version = $matches['version'][0];
		}
		// check if we have a number
		if ($version == null || $version == "") {
			$version = "?";
		}

		return array('userAgent' => $u_agent,
					 'name'      => $bname,
					 'version'   => $version,
					 'platform'  => $platform,
					 'pattern'   => $pattern);
	}


	/**
	 * Delete a file from file system
	 *
	 * @param string $file_name
	 *
	 * @return bool
	 */
	public static function deleteFile(string $file_name)
	{
		@unlink(PRIVATE_CDN_PATH.$file_name);

		return true;
	}


	public static function getClassName($object)
	{
		return strtolower((new \ReflectionClass($object))->getShortName());
	}


	/**
	 * Encrypt a message
	 *
	 * @param string $message - message to encrypt
	 * @param string $key     - encryption key
	 *
	 * @return string
	 * @throws RangeException
	 */
	public static function safeEncrypt(string $message, string $key = null)	{
		$key = $key ? $key : SODIUM_CRYPTO_KEY;

		if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
			throw new \RangeException('Key is not the correct size (must be 32 bytes).');
		}
		$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

		$cipher = base64_encode($nonce.sodium_crypto_secretbox($message, $nonce, $key));
		sodium_memzero($message);
		sodium_memzero($key);

		return $cipher;
	}


	/**
	 * Decrypt a message
	 *
	 * @param string $encrypted - message encrypted with safeEncrypt()
	 * @param string $key       - encryption key
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function safeDecrypt(string $encrypted, string $key = null){
		$key = $key ? $key: hex2bin(SODIUM_CRYPTO_KEY);

		$decoded    = base64_decode($encrypted);
		$nonce      = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
		$ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

		$plain = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
		if (!is_string($plain)) {
			throw new Exception('Invalid MAC');
		}
		sodium_memzero($ciphertext);
		sodium_memzero($key);

		return $plain;
	}


	/**
	 * @param MIXED  $vars
	 * @param STRING $codline Optional
	 */
	public static function dumpExit($vars)
	{
		$vars = count($args = func_get_args()) > 1 ? $args : $vars;
                print('<pre>');
		var_dump($vars);
                print('</pre>');
		exit('<br/>dump from dumpExit');
	}


	public static function isCPF($cpf)
	{
		$cpf = preg_replace('/[^0-9]/', '', (string)$cpf);
		// Valida tamanho
		if (strlen($cpf) != 11) {
			return false;
		}
		// Calcula e confere primeiro dígito verificador
		for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--) {
			$soma += $cpf{$i} * $j;
		}
		$resto = $soma % 11;
		if ($cpf{9} != ($resto < 2 ? 0 : 11 - $resto)) {
			return false;
		}
		// Calcula e confere segundo dígito verificador
		for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--) {
			$soma += $cpf{$i} * $j;
		}
		$resto = $soma % 11;

		return $cpf{10} == ($resto < 2 ? 0 : 11 - $resto);
	}


	public static function isCNPJ($cnpj)
	{
		$cnpj = preg_replace('/[^0-9]/', '', (string)$cnpj);
		// Valida tamanho
		if (strlen($cnpj) != 14) {
			return false;
		}
		// Valida primeiro dígito verificador
		for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
			$soma += $cnpj{$i} * $j;
			$j    = ($j == 2) ? 9 : $j - 1;
		}
		$resto = $soma % 11;
		if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto)) {
			return false;
		}
		// Valida segundo dígito verificador
		for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
			$soma += $cnpj{$i} * $j;
			$j    = ($j == 2) ? 9 : $j - 1;
		}
		$resto = $soma % 11;

		return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
	}


	public static function isEmail($email)
	{
		// SET INITIAL RETURN VARIABLES
		$emailIsValid = false;

		// MAKE SURE AN EMPTY STRING WASN'T PASSED
		if (!empty($email)) {
			// GET EMAIL PARTS
			$domain = ltrim(stristr($email, '@'), '@').'.';
			$user   = stristr($email, '@', true);

			// VALIDATE EMAIL ADDRESS
			if (!empty($user) && !empty($domain) && checkdnsrr($domain)) {
				$emailIsValid = true;
			}
		}

		// RETURN RESULT
		return $emailIsValid;
	}

        /**
         * @param STRING $str_date Recebe uma string no formato Y-m-d
         * @return STRING devolve uma string data no formatod/m/Y
         * @TODO Necessario mais tratativas
         */
        public static function dateToBrFormat($str_date){
            $str_date=substr($str_date,0,19);
            $str_date.=  strlen($str_date)<19 ? " 00:00:00" : "";
            $a=\DateTime::createFromFormat('Y-m-d', $str_date);
            return $a===false ? $str_date : $a->format('d/m/Y');
        }
        
        public static function  addMask($value, $mask, $autocomplete = false, $pad_type = STR_PAD_LEFT) {
            $len_val = strlen($value);
            $len_mask = strlen(preg_replace('/[^9aA#]/', '', $mask));

            if ($len_val === $len_mask) {
              $result = '';

              $arr_mask = str_split($mask);
              $arr_value = str_split($value);

              $x = 0;
              for($i = 0, $j = count($arr_mask); $i < $j; $i++) {

                if ($arr_mask[$i] === '9' && is_numeric($arr_value[$x])) {
                  $result .= $arr_value[$x];
                  $x++;
                }
                elseif ($arr_mask[$i] === '#' && preg_match('/[0-9a-zA-Z]/', $arr_value[$x])) {
                  $result .= $arr_value[$x];
                  $x++;
                }
                elseif (($arr_mask[$i] === 'A' || $arr_mask[$i] === 'a') && preg_match('/[a-zA-Z]/', $arr_value[$x])) {
                  $result .= $arr_value[$x];
                  $x++;
                }
                elseif (!preg_match('/[9aA#]/', $arr_mask[$i])) {
                  $result .= $arr_mask[$i];
                }
                else {
                  throw new BadMethodCallException('Caracter invalido');
                }
              }
              return $result;
            }
            else {
              if ($autocomplete === false) {
                throw new BadMethodCallException('Tamanho da mascara deve ser igual a do valor');
              }
              else {
                return self::addMask(str_pad($value, $len_mask, $autocomplete, $pad_type), $mask);
              }
            }
    }
    
    public static function addMaskCNPJ($value) { return self::addMask($value, '99.999.999/9999-99'); }
    public static function addMaskCPF($value) { return self::addMask($value, '999.999.999-99'); }
}