<?php
namespace Scriber\Bundle\CoreBundle\User\Response\MyAccount;

class UpdateSuccessResponse implements \JsonSerializable
{
    /**
     * @var string
     */
    private $token;

    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'token' => $this->token
        ];
    }
}
