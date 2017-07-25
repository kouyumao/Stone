<?php
    namespace Stone;
    
    class Configuration
    {
        private $env=[];
        private $prefix = '';
        private $defaultCfg = [];
        private $userCfg = [];
        
        function __construct($defaultCfg=[], $userCfg=[])
        {
            if(is_string($defaultCfg) && empty($userCfg)) {
                $userCfg = $defaultCfg;
            }
            if(is_string($userCfg)){
                $userCfg = [
                    'STONE_PREFIX'=>$userCfg
                ];
            }
            $this->defaultCfg = $defaultCfg;
            $this->userCfg = $userCfg;
            $this->defineConstantFromEnv();
            $this->prefix = $this->prefix();
        }
        
        protected function prefix()
        {
            $prefix = '';
            if(isset($this->defaultCfg['STONE_PREFIX'])) {
                $prefix = $this->defaultCfg['STONE_PREFIX'];
            }
            if(isset($this->userCfg['STONE_PREFIX'])) {
                $prefix = $this->userCfg['STONE_PREFIX'];
            }
            return $prefix;
        }
        
        function parseInline(string $template, array $variables=[])
        {
            return preg_replace_callback("/#\{([a-zA-Z0-9_-]+)\}/", function($matches) use ($variables){
                $key = $matches[1];
                if(isset($variables[$key])) {
                    return $variables[$key];
                }
                return $this->getDefinedValue($key);
            }, $template);
        }
        
        function __get($name)
        {
            return $this->getDefinedValue($name);
        }
        
        function defineConstantFromEnv()
        {
            foreach($_ENV as $key=>$value) {
                if(!defined($key)) {
                    define($key, $value);
                }
            }
        }
        
        function getDefinedValue($key, $defaultValue=null)
        {
            if(!empty($this->prefix)) {
                $prefixKey = $this->prefix.'_'.$key;
                if(isset($this->userCfg[$prefixKey])) {
                    return $this->userCfg[$prefixKey];
                }
                if(defined($prefixKey)) {
                    return constant($this->prefix.'_'.$key);
                }
            }
            
            if(isset($this->userCfg[$key])) {
                return $this->userCfg[$key];
            }
            
            if(defined($key)) {
                return constant($key);
            }
            
            if(isset($this->defaultCfg[$key])) {
                return $this->defaultCfg[$key];
            }
            
            if(is_null($defaultValue)) {
                throw new \Exception('Configuration '.$key.' Missing.');
            }
            
            return $defaultValue;
        }
    }