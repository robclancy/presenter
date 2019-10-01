<?php

use Robbo\Presenter\Presenter;
use PHPUnit\Framework\TestCase;

class PresenterTest extends TestCase
{
    public function testPresenterVariableCalls()
    {
        $presenter = new PresenterStub(new InjectStub);

        $this->assertEquals($presenter->testVar, 'testvar');
        $this->assertEquals($presenter['testVar'], 'testvar');
        $this->assertEquals($presenter->testVar2, 'testvar2');
    }

    public function testPresenterMethodCalls()
    {
        $presenter = new PresenterStub(new InjectStub);

        $this->assertEquals($presenter->testMethod(), 'testMethod');
        $this->assertEquals($presenter->testMethod2(), 'testMethod2');
    }

    public function testNestedPresenterVariableCalls()
    {
        $presenter = new PresenterStub(new PresenterStub2(new InjectStub));

        $this->assertEquals($presenter->testVar, 'testvar');
        $this->assertEquals($presenter->testVar2, 'testvar2');
        $this->assertEquals($presenter->testVar3, 'testvar3');
    }

    public function testNestedPresenterMethodCalls()
    {
        $presenter = new PresenterStub(new PresenterStub2(new InjectStub));

        $this->assertEquals($presenter->testMethod(), 'testMethod');
        $this->assertEquals($presenter->testMethod2(), 'testMethod2');
        $this->assertEquals($presenter->testMethod3(), 'testMethod3');
    }

    public function testPresentVariableCalls()
    {
        $presenter = new PresenterStub(new PresenterStub2(new InjectStub));

        $this->assertEquals($presenter->awesome, 'presenting you the awesome');
        $this->assertEquals($presenter['awesome'], 'presenting you the awesome');
    }

    public function testArrayPresenterVariableCalls()
    {
        $presenter = new PresenterStub(['testVar' => 'testvar']);

        $this->assertEquals($presenter->testVar, 'testvar');
        $this->assertEquals($presenter['testVar'], 'testvar');
        $this->assertEquals($presenter->testVar2, 'testvar2');
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testArrayMethodCallException()
    {
        $presenter = new PresenterStub(['testVar' => 'testvar']);
        $presenter->someMethod();
    }

    public function testArrayIsset()
    {
        $presenter = new PresenterStub(['testVar' => 'testvar']);

        $this->assertTrue(isset($presenter['testVar']));
        $this->assertFalse(isset($presenter['unsetVar']));
        $this->assertTrue(isset($presenter['awesome']));

        $presenter = new PresenterStub(new InjectStub);
        $this->assertTrue(isset($presenter['unsetVar']));
    }

    public function testObjectIsset()
    {
        $presenter = new PresenterStub(new InjectStub);

        $this->assertTrue(isset($presenter->testVar));
        $this->assertTrue(isset($presenter->awesome));
        $this->assertFalse(isset($presenter->unsetVar));
    }

    public function testArraySet()
    {
        $presenter = new PresenterStub(['testVar' => 'testvar']);
        $presenter['testNewVar'] = 'number 2';

        $this->assertEquals($presenter['testNewVar'], 'number 2');
        $this->assertEquals($presenter->testNewVar, 'number 2');
    }

    public function testArrayUnset()
    {
        $presenter = new PresenterStub(['testVar' => 'testvar']);

        $this->assertEquals($presenter['testVar'], 'testvar');

        unset($presenter['testVar']);
        $this->assertFalse(isset($presenter['testVar']));

        $presenter = new PresenterStub(new InjectStub);

        $this->assertEquals($presenter->testVar, 'testvar');
        
        unset($presenter['testVar']);
        $this->assertFalse(isset($presenter->testVar));
    }
}

class InjectStub
{
    public $testVar = 'testvar';

    public function testMethod()
    {
        return 'testMethod';
    }
}

class PresenterStub extends Presenter
{
    public $testVar2 = 'testvar2';

    public function testMethod2()
    {
        return 'testMethod2';
    }

    protected function presentAwesome()
    {
        return 'presenting you the awesome';
    }
}

class PresenterStub2 extends Presenter
{
    public $testVar3 = 'testvar3';

    public function testMethod3()
    {
        return 'testMethod3';
    }
}
