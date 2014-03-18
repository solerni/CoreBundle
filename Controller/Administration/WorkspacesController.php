<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Controller\Administration;

use Claroline\CoreBundle\Event\StrictDispatcher;
use Claroline\CoreBundle\Manager\WorkspaceManager;
use Claroline\CoreBundle\Persistence\ObjectManager;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation as SEC;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @DI\Tag("security.secure_service")
 * @SEC\PreAuthorize("hasRole('ADMIN')")
 */
class WorkspacesController
{
    private $workspaceManager;
    private $om;
    private $eventDispatcher;

    /**
     * @DI\InjectParams({
     *     "workspaceManager"   = @DI\Inject("claroline.manager.workspace_manager"),
     *     "om"                 = @DI\Inject("claroline.persistence.object_manager"),
     *     "eventDispatcher"    = @DI\Inject("claroline.event.event_dispatcher")
     * })
     */
    public function __construct(
        WorkspaceManager $workspaceManager,
        ObjectManager $om,
        StrictDispatcher $eventDispatcher
    )
    {
        $this->workspaceManager = $workspaceManager;
        $this->om = $om;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @EXT\Route(
     *     "/page/{page}/max/{max}/order/{order}",
     *     name="claro_admin_workspaces_management",
     *     defaults={"page"=1, "search"="", "max"=50, "order"="id"},
     *     options = {"expose"=true}
     * )
     * @EXT\Method("GET")
     * @EXT\Route(
     *     "/page/{page}/search/{search}/max/{max}/order/{order}",
     *     name="claro_admin_workspaces_management_search",
     *     defaults={"page"=1, "search"="", "max"=50, "order"="id"},
     *     options = {"expose"=true}
     * )
     * @EXT\Method("GET")
     * @EXT\Template
     *
     * @param $page
     * @param $search
     * @param $max
     * @param $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function managementAction($page, $search, $max, $order)
    {
        $pager = $search === '' ?
            $this->workspaceManager->findAllWorkspaces($page, $max, $order) :
            $this->workspaceManager-> getWorkspaceByName($search, $page, $max, $order);

        return array('pager' => $pager, 'search' => $search, 'max' => $max, 'order' => $order);
    }

    /**
     * @EXT\Route(
     *     "/visibility",
     *      name="claro_admin_workspaces_management_visibility",
     * )
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toggleWorkspaceVisibilityAction(Request $request)
    {
        $postData = $request->request->all();
        $workspace = $this->workspaceManager->getWorkspaceById($postData['id']);
        $postData['visible'] === '1' ?
            $workspace->setDisplayable(false) :
            $workspace->setDisplayable(true);
        $this->om->flush();

        return new Response('Visibility changed', 204);
    }

    /**
     * @EXT\Route(
     *     "/registration",
     *      name="claro_admin_workspaces_management_registration",
     * )
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response with the css class to apply to the element
     */
    public function toggleWorkspacePublicRegistrationAction(Request $request)
    {
        $postData = $request->request->all();
        $workspace = $this->workspaceManager->getWorkspaceById($postData['id']);
        $postData['registration'] === 'unlock' ?
            $workspace->setSelfRegistration(false) :
            $workspace->setSelfRegistration(true);
        $this->om->flush();

        return new Response('Registration status changed', 204);
    }

    /**
     * @EXT\Route(
     *     "/",
     *     name="claro_admin_delete_workspaces",
     *     options = {"expose"=true}
     * )
     * @EXT\Method("DELETE")
     * @EXT\ParamConverter(
     *     "workspaces",
     *      class="ClarolineCoreBundle:Workspace\AbstractWorkspace",
     *      options={"multipleIds" = true}
     * )
     *
     * Removes many workspaces from the platform.
     *
     * @param array $workspaces
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteWorkspacesAction(array $workspaces)
    {
        if (count($workspaces) > 0) {
            $this->om->startFlushSuite();

            foreach ($workspaces as $workspace) {
                $this->eventDispatcher->dispatch('log', 'Log\LogWorkspaceDelete', array($workspace));
                $this->workspaceManager->deleteWorkspace($workspace);
            }

            $this->om->endFlushSuite();
        }

        return new Response('Workspace(s) deleted', 204);
    }
}
