<?php
namespace Scriber\Bundle\CoreBundle\Twig;

use Scriber\Bundle\CoreBundle\Exception\UserNotLoggedInException;
use Scriber\Bundle\CoreBundle\Security\JWTGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class JWTExtension extends AbstractExtension
{
    /**
     * @var JWTGenerator
     */
    private $generator;

    /**
     * @param JWTGenerator $generator
     */
    public function __construct(JWTGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('scriber_core_jwt', [$this, 'generateJWT'])
        ];
    }

    /**
     * @return string
     */
    public function generateJWT(): string
    {
        try {
            return $this->generator->generateJWT();
        } catch (UserNotLoggedInException $e) {
            return '';
        }
    }
}
