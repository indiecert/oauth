<?php

namespace fkooman\IndieCert\OAuth;

use fkooman\Rest\Plugin\Authentication\AuthenticationPluginInterface;
use fkooman\Tpl\TemplateManagerInterface;
use fkooman\OAuth\OAuthService;
use fkooman\OAuth\OAuthServer;
use fkooman\Http\Request;

class Service extends OAuthService
{
    /** @var \fkooman\Tpl\TemplateManagerInterface */
    private $templateManager;

    public function __construct(OAuthServer $server, AuthenticationPluginInterface $authenticationPlugin, TemplateManagerInterface $templateManager)
    {
        parent::__construct($server, $authenticationPlugin);

        $this->templateManager = $templateManager;
        $this->registerMyRoutes();
    }

    public function registerMyRoutes()
    {
        $this->get(
            '/identify',
            function (Request $request) {
                return $this->templateManager->render(
                    'getIdentify',
                    array(
                        'redirectTo' => urldecode($request->getUrl()->getQueryParameter('redirect_to')),
                        'me' => $request->getUrl()->getQueryParameter('me'),
                    )
                );
            },
            array(
                'fkooman\Rest\Plugin\Authentication\AuthenticationPlugin' => array('enabled' => false),
            )
        );
    }
}
