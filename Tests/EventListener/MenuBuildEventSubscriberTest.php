<?php
namespace Scriber\Bundle\CoreBundle\Tests\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rzeka\Menu\MenuItemInterface;
use Scriber\Bundle\AdminPanelBundle\Event\TopMenuFinishBuildEvent;
use Scriber\Bundle\CoreBundle\EventListener\MenuBuildEventSubscriber;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuildEventSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents()
    {
        $expected = [
            TopMenuFinishBuildEvent::class => [
                'onMenuBuild',
            ],
        ];
        $result = MenuBuildEventSubscriber::getSubscribedEvents();

        static::assertEquals($expected, $result);
    }

    public function testOnMenuBuild()
    {
        /** @var MockObject|TranslatorInterface $translator */
        $translator = $this->createMock(TranslatorInterface::class);

        /** @var MockObject|UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        /** @var MockObject|LogoutUrlGenerator $logoutUrlGenerator */
        $logoutUrlGenerator = $this->createMock(LogoutUrlGenerator::class);

        /** @var MockObject|TopMenuFinishBuildEvent $event */
        $event = $this->createMock(TopMenuFinishBuildEvent::class);

        /** @var MockObject|MenuItemInterface $menu */
        $menu = $this->createMock(MenuItemInterface::class);

        $accountTitle = 'My account';
        $accountLink = '/account';

        $logoutTitle = 'Logout';
        $logoutLink = '/logout';

        $event
            ->expects(static::once())
            ->method('getMenu')
            ->willReturn($menu);

        $translator
            ->expects(static::atLeastOnce())
            ->method('trans')
            ->withConsecutive(
                ['my_account.top_menu.label', [], 'admin'],
                ['action.logout', [], 'admin']
            )
            ->willReturnOnConsecutiveCalls(
                $accountTitle,
                $logoutTitle
            );

        $urlGenerator
            ->expects(static::once())
            ->method('generate')
            ->with('scriber_core_admin_panel_myaccount')
            ->willReturn($accountLink);

        $logoutUrlGenerator
            ->expects(static::once())
            ->method('getLogoutPath')
            ->willReturn($logoutLink);

        $menu
            ->expects(static::atLeastOnce())
            ->method('addChild')
            ->withConsecutive(
                static::callback($this->getChildCheckCallback($accountTitle, $accountLink)),
                static::callback($this->getChildCheckCallback($logoutTitle, $logoutLink))
            );

        $listener = new MenuBuildEventSubscriber($translator, $urlGenerator, $logoutUrlGenerator);
        $listener->onMenuBuild($event);
    }

    /**
     * @param $title
     * @param $url
     *
     * @return \Closure
     */
    private function getChildCheckCallback($title, $url, array $attributes = [])
    {
        return function ($v) use ($title, $url, $attributes) {
            return $v instanceof MenuItemInterface &&
                   $v->getTitle() === $title &&
                   $v->getLink() === $url &&
                   $v->getAttributes() === $attributes;
        };
    }
}
