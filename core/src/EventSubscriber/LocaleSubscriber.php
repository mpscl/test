<?php

namespace App\EventSubscriber;

use App\Helpers\AppHelper;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(string $defaultLocale = AppHelper::LANG_CA)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void {
        $request = $event->getRequest();

        if(!$request->hasPreviousSession()){
            return;
        }

        $locale = $request->query->get('lang', $this->defaultLocale);

        if(!in_array($locale, [AppHelper::LANG_ES, AppHelper::LANG_CA])){
            $locale = $this->defaultLocale;
        }

        $request->getSession()->set('lang', $locale);

        $request->setLocale($locale);
    }

    /**
     * Gets subscribed events.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}