<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginSuccessListener
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack, private TranslatorInterface $translator)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function onLoginSuccess(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->flashBag->add('success', $this->translator->trans('base.signin_successful', ['%user%' => $user->getWcaId()]));
    }
}
