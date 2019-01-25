<?php

namespace App\Pages\Demo;

use PhpBg\MiniHttpd\Controller\AbstractController;
use PhpBg\MiniHttpd\Middleware\ContextTrait;
use Psr\Http\Message\ServerRequestInterface;

final class Demo extends AbstractController
{
    use ContextTrait;

    private $wsUrl;

    public function __construct(string $wsUrl)
    {
        $this->wsUrl = $wsUrl;
    }

    /**
     * Main demo page
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $context = $this->getContext($request);
        $context->renderOptions['bottomScripts'] = [
            "/vue-2.5.17.js"
        ];
        $context->renderOptions['headCss'] = ['/w3-4.11.css'];
        return [
            'wsUrl' => $this->wsUrl
        ];
    }
}