<?php
namespace TRegx\CleanRegex\Replace\NonReplaced;

use InvalidArgumentException;
use TRegx\CleanRegex\Internal\StringValue;

class MapReplaceStrategy implements NonReplacedStrategy
{
    /** @var array */
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
        $this->validateMap($map);
    }

    public function replacementResult(string $occurrence): ?string
    {
        if (array_key_exists($occurrence, $this->map)) {
            return $this->map[$occurrence];
        }
        return null;
    }

    private function validateMap(array $map): void
    {
        foreach ($map as $occurrence => $replacement) {
            $this->validateOccurrence($occurrence);
            $this->validateReplacement($replacement);
        }
    }

    private function validateOccurrence($occurrence): void
    {
        if (!is_string($occurrence)) {
            $value = (new StringValue($occurrence))->getString();
            throw new InvalidArgumentException("Invalid replacement map key. Expected string, but $value given");
        }
    }

    private function validateReplacement($replacement): void
    {
        if (!is_string($replacement)) {
            $value = (new StringValue($replacement))->getString();
            throw new InvalidArgumentException("Invalid replacement map value. Expected string, but $value given");
        }
    }
}
