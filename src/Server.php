<?php

namespace App;

use App\Pages\Demo\Demo;
use PhpBg\MiniHttpd\HttpException\RedirectException;
use PhpBg\MiniHttpd\Logger\Console;
use PhpBg\MiniHttpd\Model\ApplicationContext;
use PhpBg\MiniHttpd\Model\Route;
use PhpBg\MiniHttpd\Renderer\Phtml\Phtml;
use PhpBg\MiniHttpd\ServerFactory;
use Psr\Log\LogLevel;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;

class Server
{
    /**
     * Server constructor.
     * @param int $httpPort TCP/IP port HTTP Server will listen on
     * @param int $wsPort TCP/IP port Websocket Server woll listen on
     */
    public function __construct(int $httpPort, int $wsPort)
    {
        if ($httpPort == $wsPort) {
            throw new \RuntimeException("Listening on the same port for HTTP and WS is not yet available");
        }
        $loop = Factory::create();

        // Setup routes
        $phtmlRenderer = new Phtml(__DIR__ . '/Pages/layout.phtml');
        $routes = [
            '/' => new Route(function () {
                throw new RedirectException('/demo');
            }),
            '/demo' => new Route(new Demo(), $phtmlRenderer),
        ];

        // Setup shared context
        $applicationContext = new ApplicationContext();
        $applicationContext->loop = $loop;
        $applicationContext->options = ['wsUrl' => "ws://localhost:{$wsPort}"];
        $applicationContext->routes = $routes;
        $applicationContext->publicPath = __DIR__ . '/../public';
        $applicationContext->logger = new Console(LogLevel::DEBUG);
        $applicationContext->defaultRenderer = $phtmlRenderer;
        $server = ServerFactory::create($applicationContext);

        // Start HTTP Server
        $socket = new \React\Socket\Server("tcp://0.0.0.0:$httpPort", $loop);
        $server->listen($socket);
        $applicationContext->logger->notice("HTTP server started");

        // Start socket server
        new IoServer(new HttpServer(new WsServer(new Chat($applicationContext->logger))), new \React\Socket\Server("tcp://0.0.0.0:$wsPort", $loop), $loop);
        $applicationContext->logger->notice("Socket server started");

        if (extension_loaded('xdebug')) {
            $applicationContext->logger->warning('The "xdebug" extension is loaded, this has a major impact on performance.');
        }
        $applicationContext->logger->notice("Now just open your browser and browse http://localhost:$httpPort");
        $loop->run();
    }
}