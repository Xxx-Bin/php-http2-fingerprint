# PHP-HTTP2-FINGERPRINT
Get http2 fingerprint with php.
In addition to obtaining fingerprints, the rest of the code depends on the project [amphp/http-server 2.0](https://github.com/amphp/http-server),
in particular h2 protocol parsing.
[中文版本](https://github.com/Xxx-Bin/php-http2-fingerprint/blob/master/readme-zh.md)

## HOW TO USE
### Install
```bash
composer install
```

### Configure the domain name and certificate path
```php
    //index.php
    $cer_path = '/path/to/domain.com.cer';
    $key_path = '/path/to/domain.com.key';
    $PeerName = 'domain.com';
    $listen_uri = '0.0.0.0:443';

```
### Run it
run ```  php index.php  ``` , then open the link to your configured URL(https://domain.com/) in your browser 


## ABOUT THE CODE
Replace the dependency Http2Parser.php and Http2Parser.php in amphp by Autoload->addClassMap . 
Here's what changed.


### Http2Driver.php
1. Add the function headleH2PF and assign the fingerprint information to Client->h2fp.
2. Modify functions handleConnectionWindowIncrement handleHeaders, handlePriority handleSettings, used to get the fingerprint information required


### Http2Parser.php

1. Modify the function parsePriorityFrame
    1. $weight  should be an unsigned 8 bit integer
2. Modify the function parseHeaders
    1. $weight  should be an unsigned 8 bit integer
    2. When handlePriority is called, weight is increased by 256 to distinguish between when it comes from parseHeaders or parsePriorityFrame 



## DEMO and BLOG
[DEMO https://bjun.tech:9766/http2_fingerprint.php](https://bjun.tech:9766/http2_fingerprint.php)

[BLOG https://bjun.tech/blog/xphp/227](https://bjun.tech/blog/xphp/227)



