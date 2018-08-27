<?php
namespace SafeRegex\Errors\Errors;

use SafeRegex\Errors\HostError;
use SafeRegex\Exception\Factory\CompileSafeRegexExceptionFactory;
use SafeRegex\Exception\SafeRegexException;
use SafeRegex\PhpError;

abstract class CompileError implements HostError
{
    /** @var PhpError|null */
    private $error;

    public function __construct(?PhpError $error)
    {
        $this->error = $error;
    }

    protected function getError(): ?PhpError
    {
        return $this->error;
    }

    public function getSafeRegexpException(string $methodName): SafeRegexException
    {
        return (new CompileSafeRegexExceptionFactory($methodName, $this->error))->create();
    }

    public static function getLast(): CompileError
    {
        $phpError = PhpError::getLast();

        if (is_callable('error_clear_last')) {
            return new StandardCompileError($phpError);
        }

        return new OvertriggerCompileError($phpError);
    }
}
