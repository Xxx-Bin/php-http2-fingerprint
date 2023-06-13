<?php


$loader = require __DIR__ . "/vendor/autoload.php";

$loader->addClassMap([
    'Amp\\Http\\Server\\Driver\\Http2Driver'=>__DIR__.'/Http2Driver.php',
    'Amp\\Http\\Http2\\Http2Parser'=>__DIR__.'/Http2Parser.php',
]);

use Amp\Http\Server\HttpServer;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Socket;
use Psr\Log\NullLogger;



Amp\Loop::run(static function () {
    $cer_path = '/path/to/domain.com.cer';
    $key_path = '/path/to/domain.com.key';
    $PeerName = 'domain.com';
    $listen_uri = '0.0.0.0:443';


    $cert = new Socket\Certificate(
        $cer_path,
        $key_path
    );

    $context = (new Socket\BindContext)

        ->withTlsContext((new Socket\ServerTlsContext)
            ->withApplicationLayerProtocols(['h2'])
            ->withDefaultCertificate($cert)
            ->withPeerName($PeerName)
            ->withMinimumVersion(Socket\ServerTlsContext::TLSv1_1)
    );

    $servers = [
//        Socket\Server::listen("0.0.0.0:1337"),
//        Socket\Server::listen("[::]:1337"),
        Socket\Server::listen($listen_uri, $context),
//        Socket\Server::listen("[::]:443", $context),
    ];


    $server = new HttpServer($servers, new CallableRequestHandler(static function (Request $request) {

        if(isset($request->getClient()->h2fp)){
            $h2fp = [];
            $h2fp[]= $request->getClient()->h2fp['S[;]'];
            $h2fp[]= !isset($request->getClient()->h2fp['WU'])?'00':$request->getClient()->h2fp['WU'];
            $h2fp[]= empty($request->getClient()->h2fp['P[,]'])?0: implode(',',$request->getClient()->h2fp['P[,]']);
            $h2fp[]= $request->getClient()->h2fp['PS[,]'];
            $h2fp_str = implode('|',$h2fp);

        }

        if(in_array($request->getUri()->getPath(),['','/','/index.php','/http2_fingerprint.php']) ){
            return new Response(Status::OK, [
                "content-type" => "text/plain; charset=utf-8"
            ], empty($h2fp_str)?'none':$h2fp_str);
        }else{
            return new Response(Status::OK, [
                "content-type" => "text/plain; charset=utf-8"
            ], '');
        }


    }), new NullLogger());

    yield $server->start();

//     Stop the server when SIGINT is received (this is technically optional, but it is best to call Server::stop()).
    if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'){
        Amp\Loop::onSignal(\SIGINT, static function (string $watcherId) use ($server) {
            Amp\Loop::cancel($watcherId);
            yield $server->stop();
        });
    }
});
