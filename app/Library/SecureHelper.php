<?php
namespace App\Library;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class SecureHelper 
{
    public static function pack($array) {
        $json = json_encode($array);
        $encrypted = base64_encode($json);
        $encrypted = Crypt::encryptString($encrypted);
        return $encrypted;
    }

    public static function unpack($encoded) {
        try {
            $decrypted = Crypt::decryptString($encoded);
            $decrypted = base64_decode($decrypted);
            $array = json_decode($decrypted, true);
            array_walk_recursive($array, function(&$array) {
                $array = strip_tags($array);
            });
            return $array;
        } catch (DecryptException $e) {
            return null;
        }
    }

    public static function secure($value) {
        $string = base64_encode($value);
        $encrypted = Crypt::encryptString($string);
        return $encrypted;
    }

    public static function unsecure($value) {
        try {
            $decrypted = Crypt::decryptString($value);
            $string = base64_decode($decrypted);
            return $string;
        } catch (DecryptException $e) {
            return null;
        }
    }
}