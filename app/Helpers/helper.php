<?php
use App\Models\Db1\{Config_game};
// use App\Helpers\IntlDateFormatter;

//========================================== common ===============================================================
	function arrayTebak($val, $initial, $flip = false){
		if($val=="angkaDT") {
			$arraygame = array("Ace"=>1,"Jack"=>11,"Queen"=>12,"King"=>13);
		}elseif ($val=="poker") {
			$arraygame = array(11=>"j",12=>"q",13=>"k",14=>"a");
		} elseif ($val=="dominocard") {
			$arraygame 	=	[
    			0 => '-', 1 => '00', 2 => '01', 3 => '02', 4 => '03', 5 => '04', 6 => '05', 7 => '06', 8 => '11', 9 => '12', 10 => '13', 11 => '14', 12 => '15', 13 => '16', 14 => '22', 15 => '23', 16 => '24', 17 => '25', 18 => '26', 19 => '33', 20 => '34', 21 => '35', 22 => '36', 23 => '44', 24 => '45', 25 => '46', 26 => '55', 27 => '56', 28 => '66'
    		];
		}

		if ($flip) {
			$arraygame = array_flip($arraygame);
		}

		if (array_key_exists($initial,$arraygame)==0) {
			$mygame = $initial;
		} else {
			$mygame = $arraygame[$initial];
		}

		return $mygame;
	}

	function convertDigit($digit, $flip = false) {
		$arrayDigit = [
			"0"=>"zero",
			"1"=>"one",
			"2"=>"two",
			"3"=>"three",
			"4"=>"four",
			"5"=>"five",
			"6"=>"six",
			"7"=>"seven",
			"8"=>"eight",
			"9"=>"nine",
			"10"=>"tenth"
		];

		if ($flip) {
			$arrayDigit = array_flip($arrayDigit);
		}

		return $arrayDigit[$digit];
	}

	/**
	* show Printr with pre
	* @param $val, array
	* return print r with pre
	*/
	function showPrintr($val) {
		echo "<pre style='text-align:left'>";
		print_r($val);
		echo "</pre>";
	}

	function add_keys_dynamic($main_array, $keys, $value){    
	    $tmp_array = &$main_array;
	    while( count($keys) > 0 ){        
	        $k = array_shift($keys);        
	        if(!is_array($tmp_array)){
	            $tmp_array = array();
	        }
	        $tmp_array = &$tmp_array[$k];
	    }
	    $tmp_array = $value;
	    return $main_array;
	}

	function insert_using_keys($arr, $keys, $value){
	    // we're modifying a copy of $arr, but here
	    // we obtain a reference to it. we move the
	    // reference in order to set the values.
	    $a = &$arr;

	    while( count($keys) > 0 ){
	        // get next first key
	        $k = array_shift($keys);

	        // if $a isn't an array already, make it one
	        if(!is_array($a)){
	            $a = array();
	        }

	        // move the reference deeper
	        $a = &$a[$k];
	    }
	    $a = $value;

	    // return a copy of $arr with the value set
	    return $arr;
	}

	/**
	* grouping same key to single key
	* 
	* return single key
	*/
	function group_by_key ($array) {
		$result = array();
		foreach ($array as $sub) {
			foreach ($sub as $k => $v) {
				$result[$k][] = $v;
			}
		}
		return $result;
	}


	//	Get new array after filter
	//  @param $array, array
	//  @param $index, int | string
	//  @param $value, string
	//	@return new array, array
	function filter_by_value ($array, $index, $value){
	    if(is_array($array) && empty($array)>0) 
	    {
	        foreach(array_keys($array) as $key){
	            $temp[$key] = $array[$key][$index];
	            
	            if ($temp[$key] == $value){
	                $newarray[$key] = $array[$key];
	            }
	        }
	    }
	  	return $newarray;
	}

	function permute($str,$count=4) {
		if (strlen($str) < 2) {
			return array($str);
		}
		$permutations = array();
		$tail = substr($str, 1);
		$permutedata = array_unique(permute($tail,$count));
		foreach ($permutedata as $permutation) {
			$length = strlen($permutation);
			for ($i = 0; $i <= $length; $i++) {
				$perm = substr($permutation, 0, $i) . $str[0] . substr($permutation, $i);
				$permutations[] = $perm;
				$permit[] = substr($perm,0,$count);
			}
		}
		return array_unique($permit);
	}

	//   add version to every assets file
	//   @param is assets file name
	function assetVersion($asset) {
		return asset(config('sysconfig.foldername') . '/' . $asset.'?v='.config('sysconfig.app_version'));
	}

	function assetglobal($asset) {
		return asset($asset . '?v=' . config('sysconfig.app_version'));
	}

	//	Make Url in String clickable
	//	@param matches, variable thrown from preg_replace_callback
	//	@return href syntax of html with url inside
	function _make_url_clickable_cb($matches) {
		$ret = '';
		$url = $matches[2];

		if ( empty($url) ) {
			return $matches[0];
		}

		// removed trailing [.,;:] from URL
		if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === TRUE ) {
			$ret = substr($url, -1);
			$url = substr($url, 0, strlen($url) - 1);
		}

		return $matches[1] . "<a href=\"$url\" rel=\"nofollow\" style=\"color:blue;\">$url</a>" . $ret;
	} // end make_url_clickable_cb

	//	Make Web FTP in String clickable
	//	@param matches, variable thrown from preg_replace_callback
	//	@return href syntax of html with ftp link inside
	function _make_web_ftp_clickable_cb($matches) {
		$ret  = '';
		$dest = $matches[2];
		$dest = 'http://' . $dest;

		if ( empty($dest) ) {
			return $matches[0];
		}

		// removed trailing [,;:] from URL
		if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === TRUE ) {
			$ret = substr($dest, -1);
			$dest = substr($dest, 0, strlen($dest) - 1);
		}

		return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\" style=\"color:blue;\">" . $matches[2] . "</a>" . $ret;
	} // end make_web_ftp_clickable_cb

	//	AutoLink
	//	Make links in a string clickable
	//	@param ret, String
	//	@return ret, String, result from processed string
	function autolink($ret) {
		$val = ' ' . $ret;

		// in testing, using arrays here was found to be faster
		$val = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $val);
		$val = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $val);
		
		// this one is not in an array because we need it to run last, for cleanup of accidental links within links
		$val = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $val);

		return trim($val);
	} // autolink

	//	Conver String to Array
	//	@param str, String, delimete "|", ex : 'test|test2|test3,test4|test5,test6'
	//	@return final, array of String
	function convertToArray($str){
		foreach (explode(',', $str) as $pair) {
			list($key, $value) = explode('|', $pair);
			$final[$key] = $value;
		}

		return $final;
	} // end convertToArray

	function convertAdmCfg($str){
        foreach (explode(',', $str) as $pair) {
            list($key, $value) = explode(':', $pair);
            $final[$key] = $value;
        }

        return $final;
    }

	//	Curl Domain
	//	Get Data from Url using Curl
	//	@param url, String
	//	@param params, Array
	//	@return curl response
	function curl_domain($url, $params = []){
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

	    if ( ! empty($params)) {
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	    }

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	//	Curl Domain Using POST Method
	//	@param url, String
	//	@param params, Array
	//	@return response
	function curl_post($url, $params = []){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 20 );
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

		$response = curl_exec($curl);

		if (curl_errno($curl)) {
			$response = "";
		}
		
		curl_close($curl);

		return $response;
	} // end curl_post

	//	Change number to money format
	//	@param number, int/float/double
	//	@return money format, String
	function curr($number,$dec=false) {
		if ($dec) {
			$value = @number_format($number, 2);
		} elseif (! strpos($number, ",")) {
			$value = @number_format($number, 0, ',', ',');
		} else {
			$value = @number_format($number, 2, '.', ',');
		}

		return $value;
	} // end curr

	function decimalCurr($number) {
	   	return @number_format($number, 2, '.', ',');
	} // end curr

	

	//	Generate new password if forget password
	//	@param length (optional), int
	//	@return password, String
	function generatePassword ($length = 6) {
		// mulai dengan password kosong
		$password = "";

		// definisikan karakter-karakter yang diperbolehkan
		$possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

		// set up sebuah counter
		$i = 0;

		// tambahkan karakter acak ke $password sampai $length tercapai
		while ($i < $length) {
			// ambil sebuah karakter acak dari beberapa
			// kemungkinan yang sudah ditentukan tadi
			$char = substr($possible, random_int(0, strlen($possible) - 1), 1);

			// kami tidak ingin karakter ini jika sudah ada pada password
			if ( ! strstr($password, $char)) {
				$password .= $char;
				$i++;
			}
		}
		
		return $password;
	} // end generatePassword

	//	Get full date format
	//	@param date, String, ex: '2015-02-01'
	//	@return fulldate, String
	function getFullDate($date = '') {
		if ( ! $date) {
			$date = date('Y-m-d H:i:s');
		}

		$day   = getDay($date);
		$datex = date('d', strtotime($date));
		$month = getMonth($date);

		return $day . ', ' . $datex . ' ' . $month . date("'y H:i A");
	} // end getFullDate

	//	Get day name of the date (default: current date)
	//	@param date, String, ex: '2015-02-01'
	//	@return day, String
	function getDay($date = '') {
		if ( ! $date) {
			$date = date('Y-m-d');
		}

		$dateNumber = date('w', strtotime($date));

		$dayArray = [
			0 => 'Minggu',
			1 => 'Senin',
			2 => 'Selasa',
			3 => 'Rabu',
			4 => 'Kamis',
			5 => 'Jumat',
			6 => 'Sabtu'
		];

		return @$dayArray[$dateNumber];
	} // end getDay

	//	Get month name of the date (default: current date)
	//	@param date, String, ex: '2015-02-01'
	//	@return month, String
	function getMonth($date = '') {
		if ( ! $date) {
			$date = date('Y-m-d');
		}

		$monthNumber = date('m', strtotime($date));

		$monthArray = [
			1 => 'Jan',
			2 => 'Feb',
			3 => 'Mar',
			4 => 'Apr',
			5 => 'Mei',
			6 => 'Jun',
			7 => 'Jul',
			8 => 'Agu',
			9 => 'Sep',
			10 => 'Okt',
			11 => 'Nov',
			12 => 'Des'
		];

		return @$monthArray[$monthNumber];
	} // end getDay

	//	Get IP
	//	@return ISP IP / Proxy IP / Unreliable IP.
	function getIP() {
	  	// check for shared internet/ISP IP
		if (!empty($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			return $_SERVER["HTTP_CF_CONNECTING_IP"];
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// check if multiple ips exist in var
			$iplist = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			foreach ($iplist as $ip) {
				if (validate_ip($ip))
					return $ip;
			}
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED'])) {
			return $_SERVER['HTTP_X_FORWARDED'];
		} elseif (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
			return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_FORWARDED_FOR'];
		} elseif (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED'])) {
			return $_SERVER['HTTP_FORWARDED'];
		}

	  	// return unreliable ip since all else failed
		return $_SERVER['REMOTE_ADDR'];
	} // end getIP


	//  validate IP from getIP function
	//  @param ip value
	//  @return filter ip from ip validate. 
	function validate_ip($ip) {

		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	//	Check if number is odd or even
	//	@param number, int/float/double
	//	@return TRUE / FALSE, Boolean
	function chkgjl ($num,$val=false,$val2=false) {
		$_tmp = substr(((string)( $num / 2)),-2,2 );
		if ($_tmp == ".5") {
			if ($val) {
				return $val;
			}

			return TRUE;
		}

		if ($val2) {
			return $val2;
		}

		return FALSE;
	}

	//	Convert real string to random combination of letter and number
	//  @param all datatypes
	//	@return random combination of letter and number, String
	function codedvc ($str) {
		$k = "";
		$mksh = random_int(1,10);	
		if (chkgjl($mksh)) {
			$sh=random_int(337,360);
		} else {
			$sh=random_int(531,580);
		}

		$tmp = substr(dechex($sh),1);
		for ($i=33;$i<147;$i++) {
			$k .= chr($i);
		}

		$z = "~".$str."~";
		for ($i=0;$i<strlen($z);$i++) {
			if ($i==0) {
				$tmp .= dechex($sh + ord(substr($z,$i,1)));
			} else {
				if (chkgjl($i)) {
					$tmp .= dechex($sh + ord(substr($z,$i,1)) + substr(ord(substr($tmp,strlen($tmp)-1,1)),-1));
				} else {
					$tmp .= dechex($sh + ord(substr($z,$i,1)) - substr(ord(substr($tmp,strlen($tmp)-1,1)),-1));
				}
			}

			$tmp = substr($tmp,0,strlen($tmp)-3).substr($tmp,strlen($tmp)-2);
		}
		return $tmp; 
	}

	//	Convert random combination of letter and number (codedvc) to real string
	//  @param all datatypes
	//	@return real string, String
	function decodedvc ($str, $table = false, $chk = false) {	
		$sh = (float)(hexdec("1" . substr($str, 0, 2)));
		$s = "";
		$tmp = "";
		if (($sh <= 531) && ($sh >= 580)) {
			$s = "2";	
		} else if (($sh <= 337) && ($sh >= 360)) {
			$s = "1";
		} else {
			$sh = (float)(hexdec("2" . substr($str, 0, 2)));

			if (($sh <= 531) && ($sh >= 580))
				$s = "2";
			else if (($sh <= 337) && ($sh >= 360))
				$s="1";	
		}

		$k = 0;

		for ($i = 2;$i < strlen($str); $i = $i + 2) {
			$k++;

			if ($i == 2) {
				$tmp .= chr(hexdec($s . substr($str, $i, 2)) - $sh);
			} else {
				if (chkgjl($k)) {
					$tmp .= chr(hexdec($s . substr($str, $i, 2)) - $sh + substr(ord(substr($str, $i - 1, 1)), -1));
				} else {
					$tmp .= chr(hexdec($s . substr($str, $i, 2)) - $sh - substr(ord(substr($str, $i - 1, 1)), -1));
				}
			}
		}

		if ((substr($tmp, 0, 1) == "~") && (substr($tmp, -1) == "~")) {
			return substr($tmp, 1, -1);
		} else {
			return false;
		}
	}

	// ============================================= END COMMON ================================================================

	function itungmodulus($angkaUtama, $pembagi){
		if (is_string($angkaUtama) || is_string($pembagi) || is_bool($angkaUtama) || is_bool($pembagi)){
			return false;
		} else {
			if (is_int($angkaUtama) && is_int($pembagi)) {
				return $angkaUtama % $pembagi;
			} else {
				$pangkat    = max(strcspn(strrev($angkaUtama), '.'), strcspn(strrev($pembagi), '.'));
				$angkaUtama = $angkaUtama * pow(10, $pangkat);
				$pembagi    = $pembagi * pow(10, $pangkat);
				
				return $angkaUtama % $pembagi;
			}
		}
	}

	function ordinal($number) {
	    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
	    if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
	        return $number. 'th';
	    } else {
	        return $number. $ends[$number % 10];
	    }
	}

	//	Get Random Value of Numeric Range
	//	@param min, int
	//	@param max, int
	//	@param quantity, int
	//	@return result, String
	function randThis($min, $max, $quantity) {
	    $numbers = range($min, $max);
	    shuffle($numbers);
	    $slices  = array_slice($numbers, 0, $quantity);

	    $result = "";
	    foreach ($slices as $slice) {
	        $result .= $slice;
	    }
	    
	    return $result;
	} // end randThis


	//  GET label
	function labelCfgID($array,$item) {
		$filterItem = ($item > 1 ? $item : '');
		$data = [];
		foreach ($array as $listlabel) {
			$data[$listlabel] = $listlabel . $filterItem;
			return $data;
		}
	}

	function getDataByName($array, $stmt, $val) {
        $getttgconfigname = [];
        $arrayKey = [];
        for($i=0; $i< count($array); $i++) {
            if(is_array($stmt)) {
                foreach($stmt as $value) {
                    $arrayKey[$value] = $array[$i][$value];
                }
                $getttgconfigname = add_keys_dynamic($getttgconfigname, $arrayKey, $array[$i][$val]);
            } else {
                $getttgconfigname[$array[$i][$stmt]] = $array[$i][$val];
            }
        }

        return $getttgconfigname;
    }

    function isAssoc($arr){
		if(($arr[0]==1 && $arr[1]==2 && $arr[2]==3 && $arr[3]==4 && $arr[4]==5) || ($arr[0]==2 && $arr[1]==3 && $arr[2]==4 && $arr[3]==5 && $arr[4]==6))
			return true;
		else 
			return false;
	}

    function getTebak5050($stmt,$gameid,$number,$number2=false,$number3=false){
    	$infoGame = new Config_game;
    	$tebak = "-"; 
    	$arraydadu = array($number,$number2,$number3);
		$uniqueDice = array_unique($arraydadu);
    	if($stmt=="5050BS"){
			if($gameid==$infoGame::GAMEID_BR || $gameid==$infoGame::GAMEID_BR_PRIVATE) {
				$tot = array_sum($arraydadu);
				if ($number != 0 && $number2 != 0 && $number3 != 0) {
					if ($tot >= 3 and $tot <= 10) {
						$tebak = trans('text.small');
					} else if ($tot >= 11 and $tot <= 18) {
						$tebak = trans('text.big');
					} else {
						$tebak = "-"; 
					}
				}
			}elseif($gameid==$infoGame::GAMEID_DT || $gameid==$infoGame::GAMEID_DT_PRIVATE) {
				if($number==0) {
					$number = arrayTebak("angkaDT",$number);
				}
				if($number==7) {
					$tebak = trans('text.alllose'); 
				}else if ($number > 6 and $number!=7) {
					$tebak = trans('text.big'); 
				}else {
					$tebak = trans('text.small');
				}
			}elseif($gameid==$infoGame::GAMEID_SC || $gameid==$infoGame::GAMEID_SC_PRIVATE){
				if(count($uniqueDice)!=1){
					$tot = array_sum($arraydadu);
					if($tot > 10) {
						$tebak = trans('text.big'); 
					}else {
						$tebak = trans('text.small');
					}
				} else {
					$tebak = "Triple";
				}
			}else {
				if ($gameid==$infoGame::GAMEID_48D || $gameid==$infoGame::GAMEID_48D_PRIVATE) {
					$jum = 48;
				} elseif ($gameid==$infoGame::GAMEID_36D || $gameid==$infoGame::GAMEID_36D_PRIVATE) {
					$jum = 36;
				} elseif ($gameid==$infoGame::GAMEID_24D || $gameid==$infoGame::GAMEID_24D_PRIVATE) {
					$jum = 24;
				} elseif ($gameid==$infoGame::GAMEID_12D || $gameid==$infoGame::GAMEID_12D_PRIVATE) {
					$jum = 12;
				} elseif ($gameid==$infoGame::GAMEID_PD || $gameid==$infoGame::GAMEID_PD_PRIVATE) {
					$jum = 6;
				}
				if($number!=0) {
					if ($gameid==$infoGame::GAMEID_48D || $gameid==$infoGame::GAMEID_48D_PRIVATE) {
						if($number > $jum/1.5) {
							$tebak = trans('text.big'); 
						} elseif ($number > $jum/3) {
							$tebak = trans('text.medium');
						} else {
							$tebak = trans('text.small');
						}
					} else {
						$tot = $jum/2;
						if($number > $tot) {
							$tebak = trans('text.big'); 
						}else {
							$tebak = trans('text.small');
						}
					}
				}
			}
		} elseif ($stmt=="5050GG") {
			if($gameid==$infoGame::GAMEID_DT || $gameid==$infoGame::GAMEID_DT_PRIVATE){
				if($number==0) {
					$number = arrayTebak("angkaDT",$val);
				}
				if($number==7) {
					$tebak = "All Lose";
				}else if ($number%2==0 and $number!=7) {
					$tebak = trans('text.even'); 
				}else {
					$tebak = trans('text.odd');
				}
			}elseif($gameid==$infoGame::GAMEID_BR || $gameid==$infoGame::GAMEID_BR_PRIVATE) {
				$tot = array_sum($arraydadu);
				if ($number != 0 && $number2 != 0 && $number3 != 0) {
					if($tot%2==0){
						$tebak = trans('text.even');
					} else if ($tot%2==1) {
						$tebak = trans('text.odd');
					} else {
						$tebak = "-"; 
					}
				}
			}else if($gameid==$infoGame::GAMEID_SC || $gameid==$infoGame::GAMEID_SC_PRIVATE){
				if(count($uniqueDice)!=1){
					$tot = array_sum($arraydadu);
					if($tot%2==0){
						$tebak = trans('text.even');
					}else {
						$tebak = trans('text.odd');
					}
				}
			}else {
				if($number!=0) {
					if($number%2==0){
						$tebak = trans('text.even');
					}else {
						$tebak = trans('text.odd');
					}
				}
			}
		}

		return $tebak;
    }

    function getTebak5050Warna($number,$warna){
    	$angkakoma = ",".$number.",";
    	$tebak = '-';
    	foreach ($warna as $data) {
    		if (strstr($data->value,$angkakoma)) {
    			$tebak = ($data->name=="merah" ? trans('text.red') : trans('text.black'));
    		}
    	}

    	return $tebak;
    }

    function getTebakRow($number,$row){
    	$angkakoma = ",".$number.",";
    	$tebak = '-';
    	foreach ($row as $data) {
    		if (strstr($data->value,$angkakoma)) {
    			$exptebak = explode('-',$data->name)[1];
    			$tebak = $exptebak;
    		}
    	}

    	return $tebak;
    }

    function getTebakGroup($number,$group){
    	$angkakoma = ",".$number.",";
    	$tebak = '-';
    	foreach ($group as $data) {
    		if (strstr($data->value,$angkakoma)) {
    			$exptebak = str_split($data->name)[0];
    			$tebak = $exptebak;
    		}
    	}

    	return $tebak;
    }

    function getTebakDT($stmt,$number,$number2=false){
    	if($stmt=="gameDT"){
			if($number==0) {
				$number = arrayTebak("angkaDT",$number);
			}
			
			if($number2==0) {
				$number2 = arrayTebak("angkaDT",$number2);
			}
			
			if($number > $number2){
				$tebak = "Dragon";
			}elseif($number==$number2){
				$tebak = "Draw";
			}elseif($number < $number2) {
				$tebak = "Tiger";
			}
			//die($tebak.'masuk'.$number2);
		}elseif($stmt=="angkaDT"){
			if($number=="Ace"){
				$tebak = 1;
			}elseif($number=="Jack"){
				$tebak = 11;
			}elseif($number=="Queen"){
				$tebak = 12;
			}elseif($number=="King"){
				$tebak = 13;
			}else {
				$tebak = $number;
			}
		}elseif($stmt=="angkaDTflip"){
			if($number==1){
				$tebak = "Ace";
			}elseif($number==11){
				$tebak = "Jack";
			}elseif($number==12){
				$tebak = "Queen";
			}elseif($number==13){
				$tebak = "King";
			}else {
				$tebak = $number;
			}
		}

		return $tebak;
    }

    function getTebakPD($stmt,$number,$number2,$number3,$number4,$number5){
    	$tebak = '-'; $infoGame = new Config_game;

    	$angka = str_split($number)[0]; $angka2 = str_split($number2)[0]; $angka3 = str_split($number3)[0]; $angka4 = str_split($number4)[0]; $angka5 = str_split($number5)[0];
    	$warna = (str_split($number)[1]==1 ? trans('text.red') : trans('text.black')); 
    	$warna2 = (str_split($number2)[1]==1 ? trans('text.red') : trans('text.black')); 
    	$warna3 = (str_split($number3)[1]==1 ? trans('text.red') : trans('text.black')); 
    	$warna4 = (str_split($number4)[1]==1 ? trans('text.red') : trans('text.black')); 
    	$warna5 = (str_split($number5)[1]==1 ? trans('text.red') : trans('text.black'));

    	$mergeNum = array((int) $angka, (int) $angka2, (int) $angka3, (int) $angka4, (int) $angka5);
		$mergeClr = array($warna, $warna2, $warna3, $warna4, $warna5);
		$countVal = array_count_values($mergeNum);
		$uniqDice = array_unique($mergeNum);
		sort($mergeNum);
    	if ($stmt=="5050BS") {
    		$bs[] = getTebak5050("5050BS",$infoGame::GAMEID_PD,$angka);
    		$bs[] = getTebak5050("5050BS",$infoGame::GAMEID_PD,$angka2);
    		$bs[] = getTebak5050("5050BS",$infoGame::GAMEID_PD,$angka3);
    		$bs[] = getTebak5050("5050BS",$infoGame::GAMEID_PD,$angka4);
    		$bs[] = getTebak5050("5050BS",$infoGame::GAMEID_PD,$angka5);
    		//echo array_count_values($bs)['Big'];die();
    		if(@array_count_values($bs)[trans('text.big')] >= 3) $tebak = trans('text.big'); else $tebak = trans('text.small');
    	} elseif ($stmt=="5050GG") {
    		$gg[] = getTebak5050("5050GG",$infoGame::GAMEID_PD,$angka);
    		$gg[] = getTebak5050("5050GG",$infoGame::GAMEID_PD,$angka2);
    		$gg[] = getTebak5050("5050GG",$infoGame::GAMEID_PD,$angka3);
    		$gg[] = getTebak5050("5050GG",$infoGame::GAMEID_PD,$angka4);
    		$gg[] = getTebak5050("5050GG",$infoGame::GAMEID_PD,$angka5);
    		if(@array_count_values($gg)[trans('text.even')] >= 3) $tebak = trans('text.even'); else $tebak = trans('text.odd');
    	} elseif ($stmt=="5050Warna") {
    		$wr[] = $warna;
    		$wr[] = $warna2;
    		$wr[] = $warna3;
    		$wr[] = $warna4;
    		$wr[] = $warna5;
    		if(@array_count_values($wr)[trans('text.red')] >= 3) $tebak = trans('text.red'); else $tebak = trans('text.black');
    	} elseif ($stmt=="splitNumber") {
    		$tebak = array();
    		$tebak['number'] = $angka;
    		$tebak['number2'] = $angka2;
    		$tebak['number3'] = $angka3;
    		$tebak['number4'] = $angka4;
    		$tebak['number5'] = $angka5;
    		$tebak['colour'] = $warna;
    		$tebak['colour2'] = $warna2;
    		$tebak['colour3'] = $warna3;
    		$tebak['colour4'] = $warna4;
    		$tebak['colour5'] = $warna5;
    	} elseif ($stmt=="tebakPD") {
    		if(count(array_unique($mergeNum)) === 1){
				$tebak = "5 of a kind";
			} elseif (isAssoc($mergeNum)===true && count(array_unique($mergeClr)) === 1) {
				$tebak = "Straight Flush";
			} elseif ((count(array_unique($mergeNum)) === 5 && isAssoc($mergeNum)===false) && count(array_unique($mergeClr)) === 1) {
				$tebak = "Inc Straight Flush";
			} elseif (@$countVal[1]==4 || @$countVal[2]==4 || @$countVal[3]==4 || @$countVal[4]==4 || @$countVal[5]==4 || @$countVal[6]==4) {
				$tebak = "4 of a kind";
			} elseif (isAssoc($mergeNum)===true && count(array_unique($mergeClr)) !== 1) {
				$tebak = "Straight";
			} elseif (count(array_unique($mergeNum)) === 2) {
				$tebak = "Fullhouse";
			} elseif (count(array_unique($mergeClr)) === 1) {
				$tebak = "Flush";
			} elseif ((count(array_unique($mergeNum)) === 5 && isAssoc($mergeNum)===false) && count(array_unique($mergeClr)) !== 1) {
				$tebak = "Inc Straight";
			} elseif ((@$countVal[1]==3 || @$countVal[2]==3 || @$countVal[3]==3 || @$countVal[4]==3 || @$countVal[5]==3 || @$countVal[6]==3)) {
				$tebak = "3 of a kind";
			} elseif ((@$countVal[1]==2 || @$countVal[2]==2 || @$countVal[3]==2 || @$countVal[4]==2 || @$countVal[5]==2 || @$countVal[6]==2) && (count(array_unique($mergeNum)) === 3)) {
				$tebak = "2 Pairs";
			} elseif ((@$countVal[1]==2 || @$countVal[2]==2 || @$countVal[3]==2 || @$countVal[4]==2 || @$countVal[5]==2 || @$countVal[6]==2) && (count(array_unique($mergeNum)) === 4)) {
				$tebak = "1 Pair";
			}
    	}

    	return $tebak;
    }

	function changeDate($date,$code="Y-m-d"){
		return date($code,strtotime($date));
	}

	function langDesc($stmt,$word,$gameid=false){
		$infoGame = new Config_game;
		$array = [];
		switch ($stmt)
        {
            case "betdesc":
                switch ($gameid)
                {
                    case $infoGame::GAMEID_SC:
                        $array = ['50-50'=>'50-50','Dual'=>trans('text.anydice',[0=>2]),'Mono'=>trans('text.anydice',[0=>'Single']),'Double'=>trans('text.samedice',[0=>2]),'Triple'=>trans('text.samedice',[0=>3]),'Sum'=>trans('text.sumdice',[0=>3])];
                        break;
                    case $infoGame::GAMEID_DT:
                        $array = ['Nomor'=>'','50-50'=>'50-50','Angka'=>trans('text.nomor')];
                        break;
                    case $infoGame::GAMEID_BR:
                        $array = ['Nomor'=>trans('text.nomor'),'50-50'=>'50-50','Warna'=>trans('text.colour')];
                        break;
                    case $gameid==$infoGame::GAMEID_24D:
                    case $gameid==$infoGame::GAMEID_12D:
                    $array = ['Nomor'=>trans('text.nomor'),'50-50'=>'50-50','Row'=>trans('text.row'),'Group'=>trans('text.group'),'Dual'=>trans('text.dual'),'Triple'=>trans('text.triple'),'Quad'=>trans('text.quad'),'Hexa'=>trans('text.hexa')];
                        break;
                }
                break;

            case "historyStatus":
                $array = [
                    1 => trans('text.transferin'),
                    2 => trans('text.transferout'),
                    3 => trans('text.correction'),
                    4 => trans('text.correction'),
                    21 => trans('text.betstatus'),
                    22 => trans('text.win'),
                    23 => trans('text.lose'),
                    24 => trans('text.buygift'),
                    25 => trans('text.draw'),
                    26 => trans('text.topup'),
                    27 => trans('text.buyin'),
                    28 => trans('text.buyout'),
                    29 => trans('text.buyjackpot'),
                    30 => trans('text.winregularjackpot'),
                    31 => trans('text.winmegajackpot'),
                    32 => trans('text.fold')
                ];
                break;
        }
		/*if ($stmt=="betdesc") {
			if ($gameid==$infoGame::GAMEID_SC) {
				$array = ['50-50'=>'50-50','Dual'=>trans('text.anydice',[0=>2]),'Mono'=>trans('text.anydice',[0=>'Single']),'Double'=>trans('text.samedice',[0=>2]),'Triple'=>trans('text.samedice',[0=>3]),'Sum'=>trans('text.sumdice',[0=>3])];
			} elseif ($gameid==$infoGame::GAMEID_DT) {
				$array = ['Nomor'=>'','50-50'=>'50-50','Angka'=>trans('text.nomor')];
			} elseif ($gameid==$infoGame::GAMEID_BR) {
				$array = ['Nomor'=>trans('text.nomor'),'50-50'=>'50-50','Warna'=>trans('text.colour')];
			} elseif ($gameid==$infoGame::GAMEID_36D || $gameid==$infoGame::GAMEID_24D || $gameid==$infoGame::GAMEID_12D) {
				$array = ['Nomor'=>trans('text.nomor'),'50-50'=>'50-50','Row'=>trans('text.row'),'Group'=>trans('text.group'),'Dual'=>trans('text.dual'),'Triple'=>trans('text.triple'),'Quad'=>trans('text.quad'),'Hexa'=>trans('text.hexa')];
			}
		} elseif ($stmt=="historyStatus") {
			$array = [
			    1 => trans('text.transferin'),
                2 => trans('text.transferout'),
                3 => trans('text.correction'),
                4 => trans('text.correction'),
                21 => trans('text.betstatus'),
                22 => trans('text.win'),
                23 => trans('text.lose'),
                24 => trans('text.buygift'),
                25 => trans('text.draw'),
                26 => trans('text.topup'),
                27 => trans('text.buyin'),
                28 => trans('text.buyout'),
                29 => trans('text.buyjackpot'),
                30 => trans('text.winregularjackpot'),
                31 => trans('text.winmegajackpot'),
                32 => trans('text.fold')];
		}*/
		
		if (count($array)==0) {
			return $word;
		} else {
			return $array[$word];
		}
	}

	function getDayLang($date){
		$hari = trans_choice('text.dayArr',date('N',strtotime($date)));
		return $hari;
	}

    function getCardPokerDetail($card, $arrSymbol, $arrStatus, $aa=5) {
        if ($card===NULL) {
        	$status 	= 	"";
        	$arrCard	=	["-","-"];
        } else {
        	$expCard    =   explode(',',$card);
	        $status 	= 	$expCard[$aa];

	        $a = 1;
	        while ($a < count($expCard)) {
	        	($a==$aa ? $a++ : '');
	        	$numCard    =   arrayTebak("poker",$expCard[$a]);
	        	$a++;
	        	$symCard    =   $arrSymbol[$expCard[$a]];
	            $a++;

	            if ($expCard[0]=="F0" && $a>$aa) {
	                $arrCard[]  =   "-";
	            } else if ($expCard[0]=="F1" && $a>$aa+8) {
	                $arrCard[]  =   "-";
	            } else if ($expCard[0]=="F2" && $a>$aa+10) {
	                $arrCard[]  =   "-";
	            } else if ($expCard[0]=="F3" && $a>$aa+12) {
	                $arrCard[]  =   "-";
	            } else {
	                $arrCard[]  =   ucwords($numCard).ucwords(substr($symCard,0,1));
	            }
	        }
        }

        $data       =   [
            'status'    =>  $status,
            'arrCard'   =>  $arrCard
        ];

        return $data;
    }

    function getCardDominoDetail($card, $arrStatus, $arrValue){
    	if ($card===NULL) {
    		$status 	= 	'';
    		$arrCard 	= 	[0,0,0,0];
    		$value 		= 	[];
    	} else {
    		$expCard    =   explode(',',$card);
	        $status     =   $arrStatus[$expCard[count($expCard)-1]];
	        $value 		= 	[];

	        $a = 0;
	        while ($a < (count($expCard)-1)) {
	            $arrCard[]  =   arrayTebak('dominocard',$expCard[$a]);
	            if (count($arrValue)>0) {
	            	$value[]    =   $arrValue[$expCard[$a]];
	            }
	            $a++;
	        }
    	}

        $data       =   [
            'status'    =>  $status,
            'arrCard'   =>  $arrCard,
            'value'     =>  substr(array_sum($value),-1)
        ];

        return $data;
    }

    function getCardMmbDetail($card){
    	$expCard    =   explode(',',$card);

    	$kartu		=	array('AC','2C','3C','4C','5C','6C','7C','8C','9C','10C','JC','QC','KC','AD','2D','3D','4D','5D','6D','7D','8D','9D','10D','JD','QD','KD','AH','2H','3H','4H','5H','6H','7H','8H','9H','10H','JH','QH','KH','AS','2S','3S','4S','5S','6S','7S','8S','9S','10S','JS','QS','KS','JB','JR');

    	$a = 0;
        while ($a < (count($expCard))) {
            $arrCard[]  =   $kartu[$expCard[$a]];
            $a++;
        }

        $data       =   [
            'arrCard'   =>  $arrCard
        ];

        return $data;
    }

    function getCardDmqDetail($card, $arrStatus, $arrValue){
    	$kartu		=	array('AC','2C','3C','4C','5C','6C','7C','8C','9C','10C','JC','QC','KC','AD','2D','3D','4D','5D','6D','7D','8D','9D','10D','JD','QD','KD','AH','2H','3H','4H','5H','6H','7H','8H','9H','10H','JH','QH','KH','AS','2S','3S','4S','5S','6S','7S','8S','9S','10S','JS','QS','KS','JB','JR');

    	if ($card===NULL) {
    		$status 	= 	'';
    		$arrCard 	= 	[0,0,0,0];
    		$value 		= 	[];
    	} else {
    		$expCard    =   explode(',',$card);
	        $value 		= 	[];

	        $a = 0;
	        while ($a <= (count($expCard)-2)) {
	            $arrCard[]  =   $expCard[$a];
	            if (count($arrValue)>0) {
	            	$value[]    =   $arrValue[$expCard[$a]];
	            }
	            $a++;
	        }
    	}

        $data       =   [
            'arrCard'   =>  $arrCard,
            'value'     =>  substr(array_sum($value),-1)
        ];

        return $data;
    }

    function getCardBlackjackDetail($card){
    	$kartu		=	array('-','AC','2C','3C','4C','5C','6C','7C','8C','9C','10C','JC','QC','KC','AD','2D','3D','4D','5D','6D','7D','8D','9D','10D','JD','QD','KD','AH','2H','3H','4H','5H','6H','7H','8H','9H','10H','JH','QH','KH','AS','2S','3S','4S','5S','6S','7S','8S','9S','10S','JS','QS','KS','J1','J2');

    	if (strpos($card,'#')>0) {
    		$c 	= 	2;
    	} else {
    		$c 	= 	1;
    	}

    	$expSit 	= 	explode(':',$card);
    	$status 	= 	($expSit[1]>0 ? 'Player': 'Dealer');
    	$card 		=   $expSit[0];

    	for ($i=0; $i < $c; $i++) { 
    		$expSplit 	= 	explode('#',$card);
    		$expCard    =   explode(',',$expSplit[$i]);

    		$count 		= 	count($expCard);
    		
    		$a = 0;
	        while ($a <= ($count-2)) {
	        	
	            $arrCard[$i][]  =   ($a>=($count-1)) ? '': $kartu[$expCard[$a]];
	            $a++;
	        }
    	}
    	//$c==2 ? DD($arrCard) : "";

    	$data       =   [
            'arrCard'   =>  $arrCard,
            'status'   	=>  $status,
        ];

        return $data;
    }

    function getCardCapsaDetail($card){
    	$expCard    =   explode(',',explode('#',$card)[1]);
    	
    	$arrSym 	= 	['D','C','H','S'];

    	$a = 0;
    	while ($a < (count($expCard))) {
    		$sort 			 	= 	($expCard[$a]>9 && $expCard[$a]<25 ? ($expCard[$a]+130): $expCard[$a]);
            $arrCard[$sort]  	=   ucfirst(arrayTebak('poker',substr($expCard[$a],0,-1))).$arrSym[substr($expCard[$a],-1)];
            $a++;
        }
        ksort($arrCard);
        //dd($arrCard);
    	$data       =   [
            'arrCard'   =>  array_values($arrCard)
        ];

    	return $data;
    }

    function getCardSuperTen($card, $arrSymbol){
    	$expCard 	= 	explode(',',$card);

    	$status 	= 	$expCard[0];
    	$value 		= 	str_replace('Value ','',$expCard[7]);
    	$a = 1;
    	while ($a <= 6) {
    		$numCard = arrayTebak("poker",$expCard[$a]);
    		$a++;
    		$symCard = $arrSymbol[$expCard[$a]];
    		if ($status=="F0" && $a>4) {
    			$arrCard[]  =   "-";
    		} else {
    			$arrCard[] = ucwords($numCard).ucwords(substr($symCard,0,1));
    		}
    		$a++;
    	}

    	$data       =   [
            'arrCard'   =>  $arrCard,
            'value' 	=> 	$value
        ];

    	return $data;
    }

    function getCardJoker($card){
    	$expCard 	= 	explode(',',$card);

    	if ($expCard[0]=='j2') {
    		$arrCard[0] 	= 	'JR';
    	} else {
    		$arrCard[0] 	= 	strtoupper($expCard[1].substr($expCard[0],0,1));
    	}

    	$data       =   [
    		'status'   	=>  substr($card,-1)==6 ? 'Dealer': 'Player',
            'arrCard'   =>  $arrCard
        ];

    	return $data;
    }

    function getCardBaccarat($card){
    	$explodeCard = explode('#',$card);

    	$arrCard = [];

    	$playerCard = explode(',',$explodeCard[0]);
    	$a = 0;
    	while ($a < count($playerCard)) {
    		$symCard = ucwords(substr($playerCard[$a],0,1));
    		$a++;
    		$numcard = ucwords($playerCard[$a]);
    		$arrCard['player'][] = $numcard.$symCard;
    		$a++;
    	}

    	$bankerCard = explode(',',$explodeCard[1]);
    	$a = 0;
    	while ($a < count($bankerCard)) {
    		$symCard = ucwords(substr($bankerCard[$a],0,1));
    		$a++;
    		$numcard = ucwords($bankerCard[$a]);
    		$arrCard['banker'][] = $numcard.$symCard;
    		$a++;
    	}

    	$valueCard = explode(' | ',$explodeCard[3]);
    	$a = 0;
    	while ($a < count($valueCard)) {
    		$exp = explode(' - ',$valueCard[$a]);
    		$value[$exp[0]] = $exp[1];
    		$a++;
    	}

    	$data       =   [
    		'arrCard'   =>  $arrCard,
    		'statusWin'   =>  $explodeCard[2],
    		'value'   =>  $value,
        ];

    	return $data;
    }

    function getCardThreekings($card, $arrSymbol){
    	$expCard 	= 	explode(',',$card);

    	$value 	= 	str_replace('Value ','',$expCard[6]);
    	$a = 0;
    	while ($a <= 5) {
    		$numCard = arrayTebak("poker",$expCard[$a]);
    		$a++;
    		$symCard = $arrSymbol[$expCard[$a]];
    		$arrCard[] = ucwords($numCard).ucwords(substr($symCard,0,1));
    		$a++;
    	}

    	$data       =   [
            'arrCard'   =>  $arrCard,
            'value'   	=>  $value,
        ];

    	return $data;
    }

    function dataAnalysisTG($angka){
        $data['as']         = $A = substr("$angka",0,1);
        $data['kop']        = $B = substr("$angka",1,1);
        $data['kepala']     = $C = substr("$angka",2,1);
        $data['ekor']       = $D = substr("$angka",3,1);
        $CD = $C.$D;
        $jumCD = $C+$D;

        /* SHIO */
        $shio = $CD % 12;
        $angkaShio = ($CD=="00") ? 4 : (($shio == 0) ? 12 : $shio);
        $data['shio'] = $angkaShio;

        /* DASAR */
        if ($jumCD<10) {
            $data['dasBS'] = ($jumCD >= 5 ? trans('text.big'): trans('text.small'));
            $data['dasGG'] = ($jumCD % 2 == 0 ? trans('text.even'): trans('text.odd'));
        } else {
            $CC = substr("$jumCD",0,1);
            $DD = substr("$jumCD",1,1);
            $jumCD = $CC + $DD;

            $data['dasBS'] = ($jumCD >= 5 ? trans('text.big'): trans('text.small'));
            $data['dasGG'] = ($jumCD % 2 == 0 ? trans('text.even'): trans('text.odd'));
        }

        /* TENGAH - TEPI */
        $data['tepi']       = ($CD >= 25 && $CD <= 74 ? "Tengah": "Tepi");

        /* 5050 */
        $data['asGG']       = $asGG     = ($A % 2 == 0 ? trans('text.even'): trans('text.odd'));
        $data['asBS']       = $asBS     = ($A >= 5 ? trans('text.big'): trans('text.small'));

        $data['kopGG']      = $kopGG    = ($B % 2 == 0 ? trans('text.even'): trans('text.odd'));
        $data['kopBS']      = $kopBS    = ($B >= 5 ? trans('text.big'): trans('text.small'));

        $data['kepalaGG']   = $kepalaGG = ($C % 2 == 0 ? trans('text.even'): trans('text.odd'));
        $data['kepalaBS']   = $kepalaBS = ($C >= 5 ? trans('text.big'): trans('text.small'));

        $data['ekorGG']     = $ekorGG   = ($D % 2 == 0 ? trans('text.even'): trans('text.odd'));
        $data['ekorBS']     = $ekorBS   = ($D >= 5 ? trans('text.big'): trans('text.small'));

        /* SILANG - HOMO */
        $data['homoDepan']      = ($asGG == $kopGG ? "Homo": "Silang");
        $data['homoTengah']     = ($kopGG == $kepalaGG ? "Homo": "Silang");
        $data['homoBelakang']   = ($kepalaGG == $ekorGG ? "Homo": "Silang");

        /* KEMBANG - KEMPIS - KEMBAR */
        $data['kembangDepan']       = ($A < $B ? "Kembang": ($A > $B ? "Kempis": "Kembar"));
        $data['kembangTengah']      = ($B < $C ? "Kembang": ($B > $C ? "Kempis": "Kembar"));
        $data['kembangBelakang']    = ($C < $D ? "Kembang": ($C > $D ? "Kempis": "Kembar"));

        return $data;
    }

    function hashAPI($params,$web_id){
    	$hash 	= hash('sha256',$params);

    	return $hash;
    }

    function explodeGetParams($params) {
    	$params = explode('&',$params);
    	foreach ($params as $k => $val) {
    		$exp = explode('=',$val);
    		$data[$exp[0]] = @$exp[1];
    	}

    	return $data;
    }

    function hashParamsAPI($type,$params) {
    	$params = explodeGetParams($params);
    	$hash = null;
    	if ($type=="balance") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['currency'].@$params['username'].@$params['keyhash']);
    	} elseif ($type=="check_trans") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['trans_id'].@$params['keyhash']);
    	} elseif ($type=="checkPlayerIsOnline" || $type=="getDownline") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['username'].@$params['keyhash']);
    	} elseif ($type=="getAllPlayerBalance") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['keyhash']);
    	} elseif ($type=="getOnlinePlayerCount") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['device'].@$params['keyhash']);
    	} elseif ($type=="getJackpot") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['currency'].@$params['keyhash']);
    	} elseif ($type=="getTransResults") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['version_key'].@$params['keyhash']);
    	} elseif (in_array($type,['getReferralPerDay','getDailyWinLose','getTurnover'])) {
    		$hash = hash('sha256',@$params['operatorid'].@$params['username'].@$params['start_date'].@$params['end_date'].@$params['keyhash']);
    	} elseif ($type=="getTableName") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['game_id'].@$params['table_id'].@$params['keyhash']);
    	} elseif ($type=="getNumberResults") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['type_id'].@$params['game_id'].@$params['room_id'].@$params['keyhash']);
    	} elseif ($type=="getNumberDetails") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['game_id'].@$params['period'].@$params['room_id'].@$params['keyhash']);
    	} elseif ($type=="getMarketTime") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['game_id'].@$params['date'].@$params['keyhash']);
    	} elseif ($type=="getCurrentOutstandingBet") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['game_id'].@$params['keyhash']);
    	} elseif ($type=="getCurrentOutstandingBetDetails") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['game_id'].@$params['subgame_id'].@$params['room_id'].@$params['keyhash']);
    	} elseif ($type=="register") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['username'].@$params['currency'].@$params['language'].@stripslashes($params['fullname']).@$params['referral'].@$params['email'].@$params['keyhash']);
    	} elseif ($type=="transfer") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['currency'].@$params['username'].@$params['trans_id'].@$params['amount'].@$params['dir'].@$params['keyhash']);
    	} elseif ($type=="updatePlayerSetting") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['username'].@$params['language'].@stripslashes($params['fullname']).@$params['referral'].@$params['email'].@$params['keyhash']);
    	} elseif ($type=="getBonusReferral") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['username'].@$params['start_date'].@$params['end_date'].@$params['status'].@$params['keyhash']);
    	} elseif ($type=="getInvoiceTogel") {
    		$hash = hash('sha256',@$params['operatorid'].@$params['game_id'].@$params['period'].@$params['username'].@$params['keyhash']);
    	}

    	return $hash;
    }

    function identifier($max = 8)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $random_user_id = substr(str_shuffle($chars), 0, $max);
        return $random_user_id;
    }

    function getShioNew($number, $dt){
    	$dateResult = date('Y-m-d',strtotime($dt));
    	$chineseNewYearResult = getChineseNewYearDate(date('Y',strtotime($dateResult)));
    	$actualChineseYear = ($dateResult >= $chineseNewYearResult ? date('Y',strtotime($dt)): date('Y',strtotime('-1 year', strtotime($dt))));
        $range = date('Y') - $actualChineseYear;
        // dd([$dateResult,$chineseNewYearResult,$actualChineseYear]);

        $CD = substr("$number",2,2);

        if ($CD=="00") {
            $angkaShio = 4 + $range;
        } else {
            $shio = ($CD+$range) % 12;
            $angkaShio = ($shio == 0) ? 12 : $shio;
        }

        return $angkaShio;
    }

    function getChineseNewYearDate($year)
    {
    	$formatter = new IntlDateFormatter(
    		'zh-CN@calendar=chinese',
    		IntlDateFormatter::SHORT,
    		IntlDateFormatter::NONE,
    		'Europe/Berlin',
    		IntlDateFormatter::TRADITIONAL
    	);
    	$timeStamp = $formatter->parse($year.'/01/01');
    	$dateTime = date_create()->setTimeStamp($timeStamp);
    	return $dateTime->format('Y-m-d');
    }