<?php
    final class PasswordGenerator{
        static public function generatePassword($length){
            return self::generateRandomString($length);
        }

        static private function generateRandomString($length = 8) {
            $characters = '23456789abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
    }
?>
