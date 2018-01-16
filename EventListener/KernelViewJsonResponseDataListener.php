<?php
namespace Scriber\Bundle\CoreBundle\EventListener;

use Scriber\Bundle\CoreBundle\Http\JsonResponseData;
use Scriber\Bundle\CoreBundle\Http\JsonResponseDataModifierInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class KernelViewJsonResponseDataListener
{
    /**
     * @var array|array[]JsonResponseDataModifierInterface[]
     */
    private $responseModifiers;

    public function __construct()
    {
        $this->responseModifiers = [];
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event): void
    {
        if (count($this->responseModifiers) === 0 || $event->hasResponse()) {
            return;
        }

        $result = $event->getControllerResult();
        if (!$result instanceof JsonResponseData) {
            return;
        }

        $controller = $event->getRequest()->attributes->get('_controller', '');
        if (array_key_exists($controller, $this->responseModifiers)) {
            /** @var JsonResponseDataModifierInterface $modifier */
            foreach ($this->responseModifiers[$controller] as $modifier) {
                $result = $modifier->getModifiedJsonResponseData($result, $controller);
            }
        }

        $event->setResponse(new JsonResponse(
            $result->getData(),
            $result->getStatus(),
            $result->getHeaders()
        ));
    }

    /**
     * @param array $controllers
     * @param JsonResponseDataModifierInterface $modifier
     */
    public function addJsonResponseModifier(array $controllers, JsonResponseDataModifierInterface $modifier)
    {
        foreach ($controllers as $controller) {
            if (!array_key_exists($controller, $this->responseModifiers)) {
                $this->responseModifiers[$controller] = [];
            }

            $this->responseModifiers[$controller][] = $modifier;
        }
    }
}
