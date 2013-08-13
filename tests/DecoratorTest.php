<?php

use Robbo\Presenter\Decorator;
use Illuminate\Support\Collection;

class DecoratorTest extends PHPUnit_Framework_TestCase {

    public function testPresentableToPresenter()
    {
        $presenter = (new Decorator)->decorate(new PresentableStub);

        $this->assertInstanceOf('Robbo\Presenter\Presenter', $presenter);
    }

    public function testPresentablesToPresenters()
    {
        $from = array(
            'string' => 'string',
            'array' => array('test' => 'test'),
            'presentable' => new PresentableStub,
            'recurseMe' => array(array('presentable' => new PresentableStub)),
            'collection' => new Collection(array(
                'presentable' => new PresentableStub
            ))
        );

        $to = (new Decorator)->decorate($from);

        $this->assertSame($from['string'], $to['string']);
        $this->assertSame($from['array'], $to['array']);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $to['presentable']);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $to['presentable']->presentableObject);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $to['presentable']->getPresentableObject());
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $to['recurseMe'][0]['presentable']);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $to['collection']['presentable']);
    }
}