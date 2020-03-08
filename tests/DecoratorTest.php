<?php

use Robbo\Presenter\Decorator;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;

class DecoratorTest extends TestCase
{
    public function testPresentableToPresenter()
    {
        $decorator = new Decorator;
        $presenter = $decorator->decorate(new PresentableStub);

        $this->assertInstanceOf('Robbo\Presenter\Presenter', $presenter);
    }

    public function testPresentablesToPresenters()
    {
        $from = [
            'string' => 'string',
            'array' => ['test' => 'test'],
            'presentable' => new PresentableStub,
            'recurseMe' => [['presentable' => new PresentableStub]],
            'collection' => new Collection([
                'presentable' => new PresentableStub
            ])
        ];

        $decorator = new Decorator;
        $to = $decorator->decorate($from);

        $this->assertSame($from['string'], $to['string']);
        $this->assertSame($from['array'], $to['array']);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $to['presentable']);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $to['presentable']->presentableObject);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $to['presentable']->getPresentableObject());
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $to['recurseMe'][0]['presentable']);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $to['collection']['presentable']);
    }
}
