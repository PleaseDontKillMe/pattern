<?php
namespace CleanRegex;

use CleanRegex\Exception\CleanRegex\ArgumentNotAllowedException;
use CleanRegex\Internal\Pattern;
use SafeRegex\ExceptionFactory;

class ValidPattern
{
    /** @var Pattern */
    private $pattern;

    public function __construct(Pattern $pattern)
    {
        $this->pattern = $pattern;
    }

    public function isValid()
    {
        $result = @preg_match($this->pattern->originalPattern, null);
        $exception = (new ExceptionFactory())->retrieveGlobalsAndReturn('preg_match', $result);
        return $exception === null;
    }

    public static function matchableArgument($argument): string
    {
        if (is_string($argument)) {
            return $argument;
        }

        if (is_int($argument)) {
            return "$argument";
        }

        if (is_callable([$argument, '__toString'])) {
            return (string)$argument;
        }

        throw new ArgumentNotAllowedException('Argument should be a string, an integer or implement __toString() method!');
    }
}
