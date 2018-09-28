<?php
namespace TRegx\CleanRegex\Match\Details\Group;

use function array_map;

class MatchAll
{
    /*** @var array */
    private $matches;
    /** @var string|int */
    private $group;

    public function __construct(array $matches, $group)
    {
        $this->matches = $matches;
        $this->group = $group;
    }

    /**
     * @return (string|null)[]
     */
    public function all(): array
    {
        return array_map(function ($group) {
            if ($group === null || $group === "") {
                return null;
            }
            return $group[0];
        }, $this->matches[$this->group]);
    }
}