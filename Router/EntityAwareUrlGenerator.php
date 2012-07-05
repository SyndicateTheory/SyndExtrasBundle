<?php

namespace Synd\ExtrasBundle\Router;

use Symfony\Component\Routing\Generator\UrlGenerator;

class EntityAwareUrlGenerator extends UrlGenerator
{
    protected $classMethods = array();
    
    /**
     * @throws MissingMandatoryParametersException When route has some missing mandatory parameters
     * @throws InvalidParameterException When a parameter value is not correct
     */
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute)
    {
        if (is_object($parameters)) {
            $parameters = $this->getObjectVariables($parameters);
        }
        
        return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute);
    }
    
    /**
     * Extracts key/value pairs for an object
     */
    protected function getObjectVariables($object)
    {
        if (method_exists($object, 'toArray')) {
            return $object->toArray();
        }
        
        $out = array();
        
        if (!empty($this->classMethods[$class = get_class($object)])) {
            foreach ($this->classMethods[$class] as $key => $method) {
                $out[$key] = $object->$method();
            }
            
            return $out;
        }
        
        $this->classMethods[$class] = array();
        foreach (get_class_methods($object) as $method) {
            if (strpos($method, 'get') === 0) {
                $value = $object->$method();
        
                if (is_int($value) or is_string($value)) {
                    $this->classMethods[$class][$key = strtolower(substr($method, 3))] = $method;
                    $out[$key] = $method;
                }
            }
        }
        
        return $out;
    }
}