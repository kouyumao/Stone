<?php
    
    use PHPUnit\Framework\TestCase;
    use Stone\Util\CounterLimitException;
    
    class CounterTest extends TestCase
    {
        use Stone\Util\Counter;
        
        public function testIncrCounter()
        {
            $this->assertEquals(1, $this->incrCounter());
            $this->assertEquals(2, $this->incrCounter());
        }
        
        /**
         * @expectedException Stone\Util\CounterLimitException
         */
        public function testSetCounterLimit()
        {
            $this->setCounterLimit(2);
            $this->incrCounter();
            $this->incrCounter();
            $this->incrCounter();
        }
        
        public function testResetCounter()
        {
            $this->setCounterLimit(2);
            $this->incrCounter();
            $this->incrCounter();
            $this->resetCounter();
            $this->incrCounter();
            $this->assertEquals(2, $this->incrCounter());
        }
    }