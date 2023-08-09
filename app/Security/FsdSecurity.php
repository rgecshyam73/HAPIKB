<?php


namespace App\Security;


use Illuminate\Foundation\Console\EnvironmentCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FsdSecurity
{
    public $envs = [];

    public function __construct()
    {
        $this->envs = config('secureenvironment.environments');
    }

    public function decryptConfig()
    {
        collect($this->envs)->each(function($v, $k) {
            $decryptValue = $this->decrypt(env($k));
            config([$v => $decryptValue]);
        });

    }

    public function decryptConfigInfo()
    {
        return collect($this->envs)->map(function($v, $k) {
            return $this->decrypt(env($k));
        });
    }

    public function encryptConfig()
    {
        collect($this->envs)->each(function($v, $k) {
            $encryptedValue = $this->encrypt(env($k));
            $this->changeEnvFile($k, $encryptedValue);
            Log::info($k .' '. env($k) .' '. $encryptedValue);
        });
    }

    public function changeEnvFile($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key .'='.env($key), $key.'='.$value, file_get_contents($path)
            ));
        }
    }

    public function encrypt($value)
    {
        return $this->codedvc($value);
    }

    public function decrypt($encryptedValue)
    {
        return $this->decodedvc($encryptedValue);
    }

    public function createBatFile()
    {
        $content = "";
        collect($this->envs)->each(function($v, $k) use (&$content) {
            $content .= 'SETX -m '. $k .'"'. $this->encrypt($v) .'"'."\r";
        });
        $fp = fopen(base_path() . "/setenv.bat","wb");
        fwrite($fp,$content);
        fclose($fp);
    }

    public function setEnv($key, $value)
    {
        if (strtolower(PHP_OS_FAMILY) == "windows") {
            $this->setWindowsEnv($key, $value);
        } elseif (strtolower(PHP_OS_FAMILY) == "linux") {
            $this->setLinuxEnv($key, $value);
        }
    }

    private function setWindowsEnv($key, $value)
    {
        exec(' SETX -m '. $key .' "'. $value .'"');
    }

    private function setLinuxEnv($key, $value)
    {
        // Not Yet Implemented
    }

    public function array_find_deep($array, $search, $keys = array())
    {
        foreach($array as $key => $value) {
            if (is_array($value)) {
                $sub = $this->array_find_deep($value, $search, array_merge($keys, array($key)));
                if (count($sub)) {
                    return $sub;
                }
            } elseif ($value === $search) {
                return array_merge($keys, array($key));
            }
        }

        return array();
    }

    // Encode query string in url
    // @param str, String of querystring
    // @return tmp, String of encoded querystring
    public function codedvc ($str) {
        $k = '';
        $mksh = random_int(1, 10);

        if ($this->isNumberOdd($mksh)) {
            $sh = random_int(337, 360);
        } else {
            $sh = random_int(531, 580);
        }

        $tmp = substr(dechex($sh),1);

        for ($i = 33; $i < 147; $i++) {
            $k .= chr($i);
        }

        $z = "~" . $str . "~";
        for ($i = 0; $i < strlen($z); $i++) {
            if ($i == 0) {
                $tmp .= dechex($sh + ord(substr($z, $i, 1)));
            } else {
                if ($this->isNumberOdd($i)) {
                    $tmp .= dechex($sh + ord(substr($z, $i, 1)) + substr(ord(substr($tmp, strlen($tmp) - 1, 1)), -1));
                } else {
                    $tmp .= dechex($sh + ord(substr($z, $i, 1)) - substr(ord(substr($tmp, strlen($tmp) - 1, 1)), -1));
                }
            }

            $tmp = substr($tmp, 0, strlen($tmp) - 3) . substr($tmp, strlen($tmp) - 2);
        }

        return $tmp;
    } // end codedvc

    // Convert random combination of letter and number (codedvc) to real string
    // @param all datatypes
    // @return real string, String
    public function decodedvc ($str) {
        $sh = (float)(hexdec("1" . substr($str, 0, 2)));
        $s = "";
        $tmp = "";
        if (($sh <= 531) && ($sh >= 580)) {
            $s = "2";
        } else if (($sh <= 337) && ($sh >= 360)) {
            $s = "1";
        } else {
            $sh = (float)(hexdec("2" . substr($str, 0, 2)));

            if (($sh <= 531) && ($sh >= 580)) {
                $s = "2";
            } else if (($sh <= 337) && ($sh >= 360)) {
                $s="1";
            }
        }

        $k = 0;

        for ($i = 2;$i < strlen($str); $i = $i + 2) {
            $k++;

            if ($i == 2) {
                $tmp .= chr(hexdec($s . substr($str, $i, 2)) - $sh);
            } else {
                if ($this->isNumberOdd($k)) {
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

    function isNumberOdd ($num,$val=false,$val2=false) {
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


}
