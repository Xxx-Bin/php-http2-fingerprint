# PHP-HTTP2-FINGERPRINT
通过PHP获取HTTP2 fingerprint。
项目中除获取指纹部分，其余代码依赖项目[amphp/http-server 2.0](https://github.com/amphp/http-server)。特别是H2协议解析。

## 如何使用
### 安装
```bash
composer install
```

### 配置域名和证书路径
```php
    //index.php
    $cer_path = '/path/to/domain.com.cer';
    $key_path = '/path/to/domain.com.key';
    $PeerName = 'domain.com';
    $listen_uri = '0.0.0.0:443';

```
### 运行
运行 ```  php index.php  ```，随后在浏览器中打开你配置的域名链接 https://domain.com/


## 关于代码
通过 composer 中的 autoload->addClassMap替换依赖 amphp 中的 Http2Parser.php 和  Http2Parser.php，下面介绍大概修改了什么。 


### Http2Driver.php
1. 添加函数 headleH2PF，将指纹信息赋值 $Client->h2fp  。
2. 修改函数 handleConnectionWindowIncrement，handleHeaders，handlePriority，handleSettings，用于获取所需的指纹信息


### Http2Parser.php

1. 修改函数 parsePriorityFrame
   1. $weight 应该是 无符号的8位整型
2. 修改函数 parseHeaders
   1. $weight 应该是 无符号的8位整型
   2. 调用handlePriority时，$weight 会增加 256， 用于区分时来自parseHeaders 还是parsePriorityFrame 




## DEMO and BLOG
[DEMO https://bjun.tech:9766/http2_fingerprint.php](https://bjun.tech:9766/http2_fingerprint.php)

[BLOG https://bjun.tech/blog/xphp/227](https://bjun.tech/blog/xphp/227)














