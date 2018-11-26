<?php

namespace App\Pages\Demo;

use PhpBg\MiniHttpd\Controller\AbstractController;
use PhpBg\MiniHttpd\Middleware\ContextTrait;
use Psr\Http\Message\ServerRequestInterface;

class Demo extends AbstractController
{
    use ContextTrait;

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
        if (empty($context->applicationContext->options['wsUrl'])) {
            throw new \RuntimeException("wsUrl option must be defined");
        }
        return [
            'wsUrl' => $context->applicationContext->options['wsUrl']
        ];
    }
}