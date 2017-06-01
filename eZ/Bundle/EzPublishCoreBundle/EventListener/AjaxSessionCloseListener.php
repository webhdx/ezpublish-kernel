<?php

/**
 * File containing the AjaxSessionCloseListener class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * AjaxSessionCloseListener class.
 *
 * Takes care about closing session on parallel ajax requests so php's session locking does
 * not cause them to be wait for each other (thus becoming synchronous).
 *
 * Based on proposed solution to this by Tideways blog:
 * https://tideways.io/profiler/blog/slow-ajax-requests-in-your-symfony-application-apply-this-simple-fix
 *
 * Alternative solution as of PHP 7.0 is to set it during session start, however that would then need to be
 * done by Symfony natively.
 */
class AjaxSessionCloseListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $request = $event->getRequest();
        // @todo Rest client does not set X-Requested-With:XMLHttpRequest
        if (!$request->isXmlHttpRequest() && !$request->attributes->get('is_rest_request')) {
            return;
        }

        if (!$request->attributes->has('_route')) {
            return;
        }

        $route = $request->attributes->get('_route');
        if (stripos($route, 'session') !== false) {
            return;
        }

        // @todo Could support a route property like proposed in blog post: "_ajax_write" / "_session_write" / ..

        $session = $request->getSession();
        if (!$session->isStarted()) {
            return;
        }

        $session->save();
    }
}
