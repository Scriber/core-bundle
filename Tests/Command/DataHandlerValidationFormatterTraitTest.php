<?php
namespace Scriber\Bundle\CoreBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Command\DataHandlerValidationFormatterTrait;

class DataHandlerValidationFormatterTraitTest extends TestCase
{
    public function testGetValidationErrors()
    {
        $errors = [
            [
                'path' => 'test',
                'message' => 'test',
                'error' => 'TEST'
            ],
            [
                'path' => 'test',
                'message' => 'test2',
                'error' => 'TEST_2'
            ],
            [
                'path' => null,
                'message' => 'Global test',
                'error' => 'GLOBAL'
            ]
        ];

        $expected = [
            '',
            '[Validation error]',
            'Global test',
            '',
            '(test)',
            'test',
            'test2',
            ''
        ];

        $obj = new class {
            use DataHandlerValidationFormatterTrait;
        };

        $result = $obj->getValidationErrors($errors);

        static::assertEquals($expected, $result);
    }
}
