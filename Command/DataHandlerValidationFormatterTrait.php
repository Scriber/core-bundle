<?php
namespace Scriber\Bundle\CoreBundle\Command;

trait DataHandlerValidationFormatterTrait
{
    public function getValidationErrors(array $errors)
    {
        $errorsByPath = [];

        foreach ($errors as $error) {
            $path = $error['path'];
            if ($path === null) {
                $path = '-';
            }

            if (!array_key_exists($path, $errorsByPath)) {
                $errorsByPath[$path] = [];
            }

            $errorsByPath[$path][] = $error['message'];
        }

        ksort($errorsByPath);

        $message = [
            '',
            '[Validation error]',
        ];

        foreach ($errorsByPath as $path => $messages) {
            if ($path !== '-') {
                $message[] = sprintf('(%s)', $path);
            }

            foreach ($messages as $violation) {
                $message[] = $violation;
            }

            $message[] = '';
        }

        return $message;
    }
}
