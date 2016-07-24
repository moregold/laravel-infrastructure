<?php namespace Moregold\Infrastructure\Helpers;

use Illuminate\Support\Facades\DB;

trait UniqueValuesTrait
{
    /**
     * Generate random integer
     *
     * @param  integer $min Minimum value
     * @param  integer $max Maximum value
     *
     * @return integer      Random integer
     */
    public function randInt($min = 0, $max = 100)
    {
        return rand($min,$max);
    }

    /**
     * Generate random percentage
     *
     * @param  integer $min Minimum value
     * @param  integer $max Maximum value
     *
     * @return float        Random percent
     */
    public function randPercent($min = 0, $max = 1000)
    {
        return rand($min,$max) / 100;
    }

    /**
     * Retrieve current date time formatted based on Enum
     *
     * @param  string $modify Optional modifier for date time to retrieve
     *
     * @return string         formatted date timestamp
     */
    public function now($modify = 'now')
    {
        return date(DATE_RFC2822, strtotime($modify));
    }

    /**
     * Retrieve random item from array
     *
     * @param  array $array Source array
     *
     * @return mixed        Item to return
     */
    public function randomArrayItem($array = [])
    {
        return $array[rand(0,count($array) - 1)];
    }

    /**
     * Retrieve a list of column values from a given table
     *
     * @param  string $table  Name of table to source
     * @param  string $column Name of columns whose values to list
     *
     * @return array          List of values
     */
    public function listColumnFromTable($table = null,$column = 'id')
    {
        $results = DB::table($table)->lists($column);
        return $results;
    }

    /**
     * Generate a random GUID based on current time
     *
     * @return string Newly generated guid
     */
    public static function makeGuid()
    {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid, 12, 4).$hyphen
                .substr($charid, 16, 4).$hyphen
                .substr($charid, 20, 12);
        return $uuid;
    }

    /**
     * Generate unique (kind of) token
     *
     * @param  integer $length Desired length of the token
     *
     * @return string          Generated token
     */
    public static function makeToken($length = 8)
    {
        $rnd_id = crypt(uniqid(rand(),1));
        $rnd_id = strip_tags(stripslashes($rnd_id));
        $rnd_id = str_replace(".","",$rnd_id);
        $rnd_id = strrev(str_replace("/","",$rnd_id));
        $rnd_id = substr($rnd_id,0,$length);
        return $rnd_id;
    }

    /**
     * Generate a random alpha-numeric string
     *
     * @param  integer $length = 10 Length of string to return
     *
     * @return string               Newly generated string
     */
    public static function makeRandomString($length = 10)
    {
        $chars = "023456789abcdefghijkmnopqrstuvwxyz";
        $i = 0;
        $str = "";
        while ($i < $length) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
            $i++;
        }
        return $str;
    }

    /**
     * Generate a random numeric string
     *
     * @param  integer $length = 10 Length of string to return
     *
     * @return integer              Newly generated integer
     */
    public static function makeRandomNumber($length = 10)
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }
}
