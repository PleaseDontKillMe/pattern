<?php
namespace Test\Feature\TRegx\CleanRegex\Replace\Details\offset;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Match\Details\Detail;

class ReplacePatternTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetOffset_first()
    {
        // when
        pattern('\w{4,}')
            ->replace('Cześć, Tomek')
            ->first()
            ->callback(function (Detail $detail) {
                // when
                $offset = $detail->offset();
                $byteOffset = $detail->byteOffset();

                // then
                $this->assertSame(7, $offset);
                $this->assertSame(9, $byteOffset);

                // clean
                return '';
            });
    }

    /**
     * @test
     */
    public function shouldGetOffset_forEach()
    {
        // when
        pattern('\w{4,}')
            ->replace('Cześć, Tomek i Kamil')
            ->all()
            ->callback(function (Detail $detail) {
                if ($detail->index() !== 1) return '';

                // when
                $offset = $detail->offset();
                $byteOffset = $detail->byteOffset();

                // then
                $this->assertSame(15, $offset);
                $this->assertSame(17, $byteOffset);

                // clean
                return '';
            });
    }
}