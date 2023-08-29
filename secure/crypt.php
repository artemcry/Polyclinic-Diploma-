<?
class crypt_str {
    private static function __get_key_(){
        return "QqkDELtSDEz3wnkfT^M&2xYxE6d*J74UDC66w%eHQQhxY^pLHUB!2!!u21pawwrSx8L5w!DKyrEaEvzxn!wrcBuaL$5#%zkSHPpbaFyxZSCh2mRJgk6qStK9";
    }
    private static function __get_iv_(){
        return "a6wBe@QumDSEXqFP"; 
    }
    static function en($data) {
        $cipher = "AES-256-CBC";
        $options = OPENSSL_RAW_DATA;
        $encryptedData = openssl_encrypt($data, $cipher, crypt_str::__get_key_(), $options, crypt_str::__get_iv_());
        return base64_encode($encryptedData);
    }
    
    static function de($encryptedData) {
        $cipher = "AES-256-CBC";
        $options = OPENSSL_RAW_DATA;
        $decryptedData = openssl_decrypt(base64_decode($encryptedData), $cipher, crypt_str::__get_key_(), $options, crypt_str::__get_iv_());
        return $decryptedData;
    }
}
