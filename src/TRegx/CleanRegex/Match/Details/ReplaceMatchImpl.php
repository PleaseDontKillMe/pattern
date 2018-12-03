<?php
namespace TRegx\CleanRegex\Match\Details;

use TRegx\CleanRegex\Exception\CleanRegex\NonexistentGroupException;
use TRegx\CleanRegex\Internal\Match\Details\Group\ReplaceMatchGroupFactoryStrategy;
use TRegx\CleanRegex\Internal\Match\MatchAll\MatchAllFactory;
use TRegx\CleanRegex\Internal\Match\UserData;
use TRegx\CleanRegex\Internal\Model\Match\IRawMatchOffset;
use TRegx\CleanRegex\Internal\Subjectable;
use TRegx\CleanRegex\Match\Details\Group\MatchGroup;
use TRegx\CleanRegex\Match\Details\Group\ReplaceMatchGroup;

class ReplaceMatchImpl extends MatchImpl implements ReplaceMatch
{
    /** @var int */
    private $offsetModification;
    /** @var string */
    private $subjectModification;
    /** @var int */
    private $limit;

    public function __construct(Subjectable $subjectable,
                                int $index,
                                IRawMatchOffset $matches,
                                MatchAllFactory $allFactory,
                                int $offsetModification,
                                string $subjectModification,
                                int $limit)
    {
        parent::__construct($subjectable, $index, $matches, $allFactory, new UserData(), new ReplaceMatchGroupFactoryStrategy($offsetModification));
        $this->offsetModification = $offsetModification;
        $this->subjectModification = $subjectModification;
        $this->limit = $limit;
    }

    public function modifiedOffset(): int
    {
        return $this->offset() + $this->offsetModification;
    }

    public function modifiedSubject(): string
    {
        return $this->subjectModification;
    }

    /**
     * @param string|int $nameOrIndex
     * @return ReplaceMatchGroup
     * @throws NonexistentGroupException
     */
    public function group($nameOrIndex): MatchGroup
    {
        return $this->getReplaceGroup($nameOrIndex);
    }

    private function getReplaceGroup($nameOrIndex): ReplaceMatchGroup
    {
        /** @var ReplaceMatchGroup $matchGroup */
        $matchGroup = parent::group($nameOrIndex);
        return $matchGroup;
    }
}