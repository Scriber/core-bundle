<?php
namespace Scriber\Bundle\CoreBundle\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonResponseData
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $status;

    /**
     * @var array
     */
    private $headers;

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     */
    public function __construct(array $data, $status = JsonResponse::HTTP_OK, array $headers = [])
    {
        $this->data = $data;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}
