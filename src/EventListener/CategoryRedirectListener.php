<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CategoryRedirectListener
{
    public function __construct(
        private readonly array $categoryRedirects,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || $event->hasResponse()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->isMethodSafe() || $request->getPathInfo() !== '/products') {
            return;
        }

        $category = $request->query->get('category');

        if (!\is_string($category) || !isset($this->categoryRedirects[$category])) {
            return;
        }

        $query = $request->query->all();
        $query['category'] = $this->categoryRedirects[$category];

        $url = $request->getBaseUrl().$request->getPathInfo().'?'.\http_build_query($query);

        $event->setResponse(new RedirectResponse($url, Response::HTTP_MOVED_PERMANENTLY));
        $event->stopPropagation();
    }
}
