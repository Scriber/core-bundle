<?php
namespace Scriber\Bundle\CoreBundle\Http;

use Symfony\Component\HttpFoundation\Response;

class UnprocessableEntityJsonResponseData extends JsonResponseData
{
    /**
     * @param array $data
     * @param array $headers
     */
    public function __construct(array $data, array $headers = [])
    {
        parent::__construct($data, Response::HTTP_UNPROCESSABLE_ENTITY, $headers);
    }
}
