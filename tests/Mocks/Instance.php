<?php

namespace suffi\di\Tests\Mocks;

class Instance
{
    public function foo(Foo $foo, $bar = '')
    {
        return $foo->foo . $bar;
    }

    private function bar(Foo $foo, $bar = '')
    {
        return $foo->foo . $bar;
    }

    public static function sfoo(Foo $foo, $bar = '')
    {
        return $foo->foo . $bar;
    }
}
