<?php
namespace Scriber\Bundle\CoreBundle\EventListener;

use Scriber\Bundle\AdminPanelBundle\Event\TopMenuFinishBuildEvent;
use Scriber\Bundle\AdminPanelBundle\Menu\MenuItemFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuildEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var LogoutUrlGenerator
     */
    private $logoutUrlGenerator;

    /**
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     * @param LogoutUrlGenerator $logoutUrlGenerator
     */
    public function __construct(TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator, LogoutUrlGenerator $logoutUrlGenerator)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->logoutUrlGenerator = $logoutUrlGenerator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        $events = [];

        if (class_exists(TopMenuFinishBuildEvent::class)) {
            $events[TopMenuFinishBuildEvent::class] = [
                'onMenuBuild',
            ];
        }

        return $events;
    }

    /**
     * @param TopMenuFinishBuildEvent $event
     */
    public function onMenuBuild(TopMenuFinishBuildEvent $event): void
    {
        $menu = $event->getMenu();

        $myaccount = MenuItemFactory::createNewItem(
            $this->translator->trans('my_account.top_menu.label', [], 'admin'),
            $this->urlGenerator->generate('scriber_core_admin_panel_myaccount')
        );

        $logout = MenuItemFactory::createNewItem(
            $this->translator->trans('action.logout', [], 'admin'),
            $this->logoutUrlGenerator->getLogoutPath()
        );

        $menu->addChild($myaccount);
        $menu->addChild($logout);
    }
}
