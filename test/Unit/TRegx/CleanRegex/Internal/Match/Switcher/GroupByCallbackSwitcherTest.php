<?php
namespace Test\Unit\TRegx\CleanRegex\Internal\Match\Switcher;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Exception\InvalidReturnValueException;
use TRegx\CleanRegex\Internal\Exception\NoFirstSwitcherException;
use TRegx\CleanRegex\Internal\Match\Switcher\GroupByCallbackSwitcher;
use TRegx\CleanRegex\Internal\Match\Switcher\Switcher;
use TRegx\CleanRegex\Match\Details\Group\MatchGroup;
use TRegx\CleanRegex\Match\Details\Match;

class GroupByCallbackSwitcherTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAll()
    {
        // given
        $switcher = new GroupByCallbackSwitcher($this->mock('all', 'willReturn', [10 => 'One', 20 => 'Two', 30 => 'Three']), function (string $value) {
            return $value[0];
        });

        // when
        $all = $switcher->all();

        // then
        $this->assertSame(['O' => ['One'], 'T' => ['Two', 'Three']], $all);
    }

    /**
     * @test
     */
    public function shouldGroupDifferentDataTypes()
    {
        // given
        $match = $this->matchMock('hello');
        $group = $this->matchGroupMock('hello');
        $input = ['hello', 2, $match, 2, $group,];
        $switcher = new GroupByCallbackSwitcher($this->mock('all', 'willReturn', $input), function ($value) {
            return $value;
        });

        // when
        $all = $switcher->all();

        // then
        $expected = [
            'hello' => ['hello', $match, $group],
            2       => [2, 2],
        ];
        $this->assertSame($expected, $all);
    }

    /**
     * @test
     */
    public function shouldGetFirst()
    {
        // given
        $switcher = new GroupByCallbackSwitcher($this->mock('first', 'willReturn', 'One'), 'strtoupper');

        // when
        $first = $switcher->first();

        // then
        $this->assertSame('One', $first);
    }

    /**
     * @test
     */
    public function shouldGetFirstKey()
    {
        // given
        $switcher = new GroupByCallbackSwitcher($this->mock('first', 'willReturn', 'One'), 'strtoupper');

        // when
        $firstKey = $switcher->firstKey();

        // then
        $this->assertSame('ONE', $firstKey);
    }

    /**
     * @test
     */
    public function shouldFirstThrow()
    {
        // given
        $switcher = new GroupByCallbackSwitcher($this->mock('first', 'willThrowException', new NoFirstSwitcherException()), 'strlen');

        // then
        $this->expectException(NoFirstSwitcherException::class);

        // when
        $switcher->first();
    }

    /**
     * @test
     * @dataProvider callers
     * @param string $caller
     * @param $returnValue
     */
    public function shouldThrowForInvalidGroupByType_all(string $caller, $returnValue)
    {
        // given
        $switcher = new GroupByCallbackSwitcher($this->mock($caller, 'willReturn', $returnValue), function () {
            return [];
        });

        // then
        $this->expectException(InvalidReturnValueException::class);
        $this->expectExceptionMessage('Invalid groupByCallback() callback return type. Expected int|string, but array (0) given');

        // when
        $switcher->$caller();
    }

    public function callers(): array
    {
        return [
            'all()'   => ['all', ['foo']],
            'first()' => ['first', 'foo']
        ];
    }

    private function mock(string $methodName, string $setter, $value): Switcher
    {
        /** @var Switcher|MockObject $switcher */
        $switcher = $this->createMock(Switcher::class);
        $switcher->expects($this->once())->method($methodName)->$setter($value);
        $switcher->expects($this->never())->method($this->logicalNot($this->matches($methodName)));
        return $switcher;
    }

    private function matchMock(string $text): Match
    {
        /** @var Match|MockObject $switcher */
        $switcher = $this->createMock(Match::class);
        $switcher->expects($this->once())->method('text')->willReturn($text);
        $switcher->expects($this->never())->method($this->logicalNot($this->matches('text')));
        return $switcher;
    }

    private function matchGroupMock(string $text): MatchGroup
    {
        /** @var MatchGroup|MockObject $switcher */
        $switcher = $this->createMock(MatchGroup::class);
        $switcher->expects($this->once())->method('text')->willReturn($text);
        $switcher->expects($this->never())->method($this->logicalNot($this->matches('text')));
        return $switcher;
    }
}
