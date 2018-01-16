<?php
namespace Scriber\Bundle\CoreBundle\Http;

interface JsonResponseDataModifierInterface
{
    /**
     * @param JsonResponseData $data
     * @param string $controller
     *
     * @return JsonResponseData
     */
    public function getModifiedJsonResponseData(JsonResponseData $data, string $controller): JsonResponseData;
}
