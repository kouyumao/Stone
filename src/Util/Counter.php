<?php
    namespace Stone\Util;
    
    trait Counter
    {
        private $countData = [
            'default'=>0
        ];
        
        private $limitData = [];
        
        function resetCounter(string $key='default')
        {
            $this->countData[$key] = 0;
        }
        
        function incrCounter(string $key='default')
        {
            if(!isset($this->countData[$key])) {
                $this->countData[$key] = 1;
            } else {
                $this->countData[$key]++;
            }
            
            if(isset($this->limitData[$key])) {
                if( $this->countData[$key] > $this->limitData[$key]) {
                    throw new CounterLimitException();
                }
            }
            return $this->countData[$key];
        }
        
        function setCounterLimit(int $limit, string $key='default')
        {
            $this->limitData[$key] = $limit;
        }
    }