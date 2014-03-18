<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Controller\Badge;

use Claroline\CoreBundle\Rule\Validator;
use Claroline\CoreBundle\Entity\Badge\Badge;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BadgeController extends Controller
{
    public function listAction($parameters)
    {
        /** @var \Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler $platformConfigHandler */
        $platformConfigHandler = $this->get('claroline.config.platform_config_handler');

        /** @var \Claroline\CoreBundle\Repository\Badge\BadgeRepository $badgeRepository */
        $badgeRepository = $this->getDoctrine()->getRepository('ClarolineCoreBundle:Badge\Badge');

        /** @var QueryBuilder $badgeQueryBuilder */
        $badgeQueryBuilder = $badgeRepository->findOrderedByName(
            $platformConfigHandler->getParameter('locale_language'),
            false
        );

        if (isset($parameters['workspace']) && null !== $parameters['workspace']) {
            $badgeQueryBuilder
                ->andWhere('badge.workspace = :workspace')
                ->setParameter('workspace', $parameters['workspace']);

            $badgeClaimsWorkspace = $parameters['workspace'];
        } else {
            $badgeQueryBuilder->andWhere('badge.workspace IS NULL');
            $badgeClaimsWorkspace = null;
        }

        $badgeClaimQuery = $this->getDoctrine()
            ->getRepository('ClarolineCoreBundle:Badge\BadgeClaim')
            ->findByWorkspace($badgeClaimsWorkspace, false);

        $pagerFactory       = $this->get('claroline.pager.pager_factory');
        $badgePager         = $pagerFactory->createPager($badgeQueryBuilder->getQuery(), $parameters['badgePage'], 10);
        $claimPager         = $pagerFactory->createPager($badgeClaimQuery, $parameters['claimPage'], 10);
        $badgeRuleValidator = $this->get("claroline.rule.validator");

        return $this->render(
            'ClarolineCoreBundle:Badge:Template/list.html.twig',
            array(
                'badgePager'       => $badgePager,
                'claimPager'       => $claimPager,
                'parameters'       => $parameters,
                'badgeRuleChecker' => $badgeRuleValidator
            )
        );
    }
}
