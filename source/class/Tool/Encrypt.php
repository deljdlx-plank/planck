<?php

namespace Planck\Tool;


class Encrypt
{

    protected $key;
    protected $keyIv;
    protected $method = 'AES-256-CBC';

    public function __construct($key, $keyIv)
    {
        $this->key = $key;
        $this->keyIv = $keyIv;
    }


    public function encrypt( $string)
    {

        $secret_key = $this->key;
        $secret_iv = $this->keyIv;
        $encrypt_method = $this->method;



        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );


        return base64_encode(
            openssl_encrypt( $string, $encrypt_method, $key, 0, $iv )
        );

    }

    public function decrypt($string)
    {

        $secret_key = $this->key;
        $secret_iv = $this->keyIv;
        $encrypt_method = $this->method;



        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );


        return openssl_decrypt(
            base64_decode($string),
            $encrypt_method,
            $key,
            0,
            $iv
        );

    }

}
