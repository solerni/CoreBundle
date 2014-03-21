<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Listener;

use Claroline\CoreBundle\Manager\LocaleManager;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Observe;
use JMS\DiExtraBundle\Annotation\Service;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @Service
 *
 * Listener setting the platform language according to platform_options.yml.
 */
class LocaleSetter
{
    private $localeManager;
    /**
     * @InjectParams({
     *     "localeManager"   = @Inject("claroline.common.locale_manager"),
     *     "securityContext" = @Inject("security.context"),
     *     "configHandler"   = @Inject("claroline.config.platform_config_handler")
     * })
     */
    public function __construct(
        LocaleManager $localeManager, 
        SecurityContextInterface $securityContext, 
        \Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler $configHandler
    ) {
        $this->localeManager = $localeManager;
        $this->securityContext = $securityContext;
        $this->configHandler = $configHandler;
        
    }

    /**
     * @Observe("kernel.request")
     *
     * Sets the platform language.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        //if (!$this->securityContext->getToken() || 
        //    $this->securityContext->getToken()->getUser() === 'anon.') {
            
            $locale = $this->configHandler->getParameter('locale_language');
        //} else {
            //$locale = $this->localeManager->getUserLocale($request);
        //}

        $request->setLocale($locale);
    }
}
