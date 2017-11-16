<?php
namespace CleanRegex\Match;

use CleanRegex\Exception\Preg\PatternMatchException;
use CleanRegex\Internal\Pattern;
use SafeRegex\ExceptionFactory;

class MatchPattern
{
    private const WHOLE_MATCH = 0;

    /** @var Pattern */
    private $pattern;

    /** @var string */
    private $subject;

    public function __construct(Pattern $pattern, string $subject)
    {
        $this->pattern = $pattern;
        $this->subject = $subject;
    }

    /**
     * @return array
     * @throws PatternMatchException
     */
    public function all(): array
    {
        $matches = [];

        $result = @preg_match_all($this->pattern->pattern, $this->subject, $matches);
        (new ExceptionFactory())->retrieveGlobalsAndThrow('preg_match', $result);

        return $matches[0];
    }

    public function iterate(callable $callback): void
    {
        foreach ($this->getMatchObjects() as $object) {
            call_user_func($callback, $object);
        }
    }

    public function map(callable $callback): array
    {
        $results = [];
        foreach ($this->getMatchObjects() as $object) {
            $results[] = call_user_func($callback, $object);
        }
        return $results;
    }

    public function first(callable $callback = null): ?string
    {
        $matches = $this->performMatchAll();
        if (empty($matches)) return null;

        if ($callback !== null) {
            call_user_func($callback, new Match($this->subject, 0, $matches));
        }

        list($value, $offset) = $matches[self::WHOLE_MATCH][0];
        return $value;
    }

    /**
     * @return Match[]
     */
    private function getMatchObjects(): array
    {
        return $this->constructMatchObjects($this->performMatchAll());
    }

    private function performMatchAll(): array
    {
        $matches = [];

        $result = @preg_match_all($this->pattern->pattern, $this->subject, $matches, PREG_OFFSET_CAPTURE);
        (new ExceptionFactory())->retrieveGlobalsAndThrow('preg_match_all', $result);

        return $matches;
    }

    /**
     * @param array $matches
     * @return Match[]
     */
    private function constructMatchObjects(array $matches): array
    {
        $matchObjects = [];

        foreach ($matches[0] as $index => $match) {
            $matchObjects[] = new Match($this->subject, $index, $matches);
        }

        return $matchObjects;
    }

    public function matches()
    {
        $result = @preg_match($this->pattern->pattern, $this->subject);
        (new ExceptionFactory())->retrieveGlobalsAndThrow('preg_match', $result);

        return $result === 1;
    }

    public function count(): int
    {
        $result = @preg_match_all($this->pattern->pattern, $this->subject);
        (new ExceptionFactory())->retrieveGlobalsAndThrow('preg_match_all', $result);

        return $result;
    }
}
