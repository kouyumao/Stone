<?php
    namespace Stone;
    
    class Redis
    {
        protected $client;
        private $defaultConfiguration = [
            'STONE_PREFIX'=>'',
            'REDIS_URI'=>'#{REDIS_SCHEME}://#{REDIS_HOST}:#{REDIS_PORT}',
            'REDIS_HOST'=>'127.0.0.1',
            'REDIS_SCHEME'=>'tcp',
            'REDIS_PORT'=>6379,
        ];
        
        function __construct($userCfg=[])
        {
            $configure = new Configuration($this->defaultConfiguration, $userCfg);
            $URI = $configure->parseInline($configure->REDIS_URI);
            $this->client = new \Predis\Client($URI);
        }
        
        function __call($name, $args)
        {
            return call_user_func_array([$this->client, $name], $args);
        }
    }