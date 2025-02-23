<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class LogoutSubscriber implements EventSubscriberInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(private UrlGeneratorInterface $urlGenerator, RequestStack $requestStack, private TranslatorInterface $translator) {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $request = $event->getRequest();

        $response = new RedirectResponse(
            $this->urlGenerator->generate('home', ['_locale' => $request->getLocale()]),
        );
        $event->setResponse($response);

        $this->flashBag->add('success', $this->translator->trans('base.signout_successful'));
    }
}