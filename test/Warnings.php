<?php
namespace Test;

trait Warnings
{
    public function causeRuntimeWarning(): void
    {
        @preg_match('/pattern/', "\xc3\x28");
    }

    public function causePhpWarning(): void
    {
        @preg_match('/unclosed pattern', '');
    }
}
