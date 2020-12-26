<?php
namespace Test\Unit\TRegx\SafeRegex\Guard;

use PHPUnit\Framework\TestCase;
use Test\Utils\Functions;
use Test\Warnings;
use TRegx\SafeRegex\Errors\ErrorsCleaner;
use TRegx\SafeRegex\Exception\CompilePregException;
use TRegx\SafeRegex\Exception\RuntimePregException;
use TRegx\SafeRegex\Guard\GuardedInvoker;

class GuardedInvokerTest extends TestCase
{
    use Warnings;

    /**
     * @test
     */
    public function shouldNotCatchException()
    {
        // given
        $invoker = new GuardedInvoker('preg_match', '/p/', Functions::constant(13));

        // when
        [$result, $exception] = $invoker->catch();

        // then
        $this->assertSame(13, $result);
        $this->assertNull($exception);
    }

    /**
     * @test
     */
    public function shouldCatchRuntimeWarning()
    {
        // given
        $invoker = new GuardedInvoker('preg_match', '/p/', function () {
            $this->causeRuntimeWarning();
            return 14;
        });

        // when
        [$result, $exception] = $invoker->catch();

        // then
        $this->assertSame(14, $result);
        $this->assertInstanceOf(RuntimePregException::class, $exception);
        $this->assertSame("/p/", $exception->getPregPattern());
    }

    /**
     * @test
     */
    public function shouldCatchCompileWarning()
    {
        // given
        $invoker = new GuardedInvoker('preg_match', '/p/', function () {
            $this->causeCompileWarning();
            return 15;
        });

        // when
        [$result, $exception] = $invoker->catch();

        // then
        $this->assertSame(15, $result);
        $this->assertInstanceOf(CompilePregException::class, $exception);
        $this->assertSame("/p/", $exception->getPregPattern());
    }

    /**
     * @test
     */
    public function shouldReturnResult()
    {
        // given
        $invoker = new GuardedInvoker('preg_match', '/p/', Functions::constant(16));

        // when
        [$result, $exception] = $invoker->catch();

        // then
        $this->assertSame(16, $result);
        $this->assertNull($exception);
    }

    /**
     * @test
     * @dataProvider possibleObsoleteWarnings
     * @param callable $obsoleteWarning
     */
    public function shouldIgnorePreviousWarnings(callable $obsoleteWarning)
    {
        // given
        $obsoleteWarning();
        $invoker = new GuardedInvoker('preg_match', '/p/', Functions::constant(17));

        // when
        [$result, $exception] = $invoker->catch();

        // then
        $this->assertSame(17, $result);
        $this->assertNull($exception);
    }

    /**
     * @test
     * @dataProvider possibleObsoleteWarnings
     * @param callable $obsoleteWarning
     */
    public function shouldNotLeaveOutWarnings(callable $obsoleteWarning)
    {
        // given
        $invoker = new GuardedInvoker('preg_match', '/p/', function () use ($obsoleteWarning) {
            $obsoleteWarning();
        });

        // when
        $invoker->catch();

        // then
        $this->assertFalse((new ErrorsCleaner())->getError()->occurred());
    }

    public function possibleObsoleteWarnings(): array
    {
        return [
            [function () {
                $this->causeRuntimeWarning();
            }],
            [function () {
                $this->causeCompileWarning();
            }],
        ];
    }

    /**
     * @test
     */
    public function shouldNotSilenceExceptions()
    {
        // given
        $invoker = new GuardedInvoker('preg_match', '/p/', Functions::throws(new \Exception("For Frodo")));

        // then
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("For Frodo");

        // when
        $invoker->catch();
    }
}