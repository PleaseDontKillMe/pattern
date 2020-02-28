<?php
namespace TRegx\CleanRegex\Replace\By;

use TRegx\CleanRegex\Exception\GroupNotMatchedException;
use TRegx\CleanRegex\Match\ForFirst\Optional;

interface OptionalStrategySelector extends Optional
{
    public function orThrow(string $exceptionClassName = GroupNotMatchedException::class);
}
