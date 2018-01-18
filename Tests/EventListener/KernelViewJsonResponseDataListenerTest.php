<?php
namespace Scriber\Bundle\CoreBundle\Tests\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\EventListener\KernelViewJsonResponseDataListener;
use Scriber\Bundle\CoreBundle\Http\JsonResponseData;
use Scriber\Bundle\CoreBundle\Http\JsonResponseDataModifierInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class KernelViewJsonResponseDataListenerTest extends TestCase
{
    /**
     * @var MockObject|GetResponseForControllerResultEvent
     */
    private $event;

    /**
     * @var MockObject|JsonResponseDataModifierInterface
     */
    private $modifier;

    protected function setUp()
    {
        $this->event = $this->createMock(GetResponseForControllerResultEvent::class);
        $this->modifier = $this->createMock(JsonResponseDataModifierInterface::class);
    }

    protected function tearDown()
    {
        $this->event = null;
        $this->modifier = null;
    }

    public function testOnKernelViewWithoutModifiers()
    {
        $jsonResponseData = $this->createMock(JsonResponseData::class);
        $responseDataData = ['test'];
        $responseDataStatus = 200;
        $responseDataHeaders = ['test' => 'test'];

        $this->event
            ->expects(static::once())
            ->method('hasResponse')
            ->willReturn(false);

        $this->event
            ->expects(static::once())
            ->method('getControllerResult')
            ->willReturn($jsonResponseData);

        $this->event
            ->expects(static::never())
            ->method('getRequest');

        $jsonResponseData
            ->expects(static::once())
            ->method('getData')
            ->willReturn($responseDataData);

        $jsonResponseData
            ->expects(static::once())
            ->method('getStatus')
            ->willReturn($responseDataStatus);

        $jsonResponseData
            ->expects(static::once())
            ->method('getHeaders')
            ->willReturn($responseDataHeaders);

        $this->event
            ->expects(static::once())
            ->method('setResponse')
            ->with(static::callback($this->getSetResponseCallback($responseDataData, $responseDataStatus, $responseDataHeaders)));

        $listener = new KernelViewJsonResponseDataListener();
        $listener->onKernelView($this->event);
    }

    public function testOnKernelViewEventHasResponse()
    {
        $this->event
            ->expects(static::once())
            ->method('hasResponse')
            ->willReturn(true);

        $this->event
            ->expects(static::never())
            ->method('getControllerResult');

        $listener = new KernelViewJsonResponseDataListener();
        $listener->addJsonResponseModifier([''], $this->modifier);
        $listener->onKernelView($this->event);
    }

    public function testOnKernelViewControllerResultNotInstanceOfJsonResponseData()
    {
        $this->event
            ->expects(static::once())
            ->method('hasResponse')
            ->willReturn(false);

        $this->event
            ->expects(static::once())
            ->method('getControllerResult')
            ->willReturn(new class {});

        $this->event
            ->expects(static::never())
            ->method('getRequest');

        $listener = new KernelViewJsonResponseDataListener();
        $listener->addJsonResponseModifier([''], $this->modifier);
        $listener->onKernelView($this->event);
    }

    public function testOnKernelViewWithModifier()
    {
        $request = $this->createMock(Request::class);
        $request->attributes = $this->createMock(ParameterBag::class);

        $originalJsonResponseData = $this->createMock(JsonResponseData::class);

        $jsonResponseData = $this->createMock(JsonResponseData::class);
        $responseDataData = ['test'];
        $responseDataStatus = 200;
        $responseDataHeaders = ['test' => 'test'];

        $controller = 'test::controller';

        $this->event
            ->expects(static::once())
            ->method('hasResponse')
            ->willReturn(false);

        $this->event
            ->expects(static::once())
            ->method('getControllerResult')
            ->willReturn($jsonResponseData);

        $this->event
            ->expects(static::once())
            ->method('getRequest')
            ->willReturn($request);

        $request->attributes
            ->expects(static::once())
            ->method('get')
            ->willReturn($controller);

        $originalJsonResponseData
            ->expects(static::never())
            ->method('getData');

        $jsonResponseData
            ->expects(static::once())
            ->method('getData')
            ->willReturn($responseDataData);

        $jsonResponseData
            ->expects(static::once())
            ->method('getStatus')
            ->willReturn($responseDataStatus);

        $jsonResponseData
            ->expects(static::once())
            ->method('getHeaders')
            ->willReturn($responseDataHeaders);

        $this->modifier
            ->expects(static::once())
            ->method('getModifiedJsonResponseData')
            ->willReturn($jsonResponseData);

        $this->event
            ->expects(static::once())
            ->method('setResponse')
            ->with(static::callback($this->getSetResponseCallback($responseDataData, $responseDataStatus, $responseDataHeaders)));

        $listener = new KernelViewJsonResponseDataListener();
        $listener->addJsonResponseModifier([$controller], $this->modifier);
        $listener->onKernelView($this->event);
    }

    private function getSetResponseCallback($data, $status, $headers)
    {
        return function (JsonResponse $response) use ($data, $status, $headers) {
            $data = json_decode($response->getContent(), true);
            $allHeaders = $response->headers->all();

            $headersValid = true;
            foreach ($headers as $header => $values) {
                $values = (array) $values;
                if (!array_key_exists($header, $allHeaders) || $allHeaders[$header] !== $values) {
                    $headersValid = false;
                    break;
                }
            }

            return $data === $data &&
                   $status === $response->getStatusCode() &&
                   $headersValid;
        };
    }
}
