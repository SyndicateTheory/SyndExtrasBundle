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
            $parameters = $this->getObjectVariables($parameters, $variables);
        }
        
        return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute);
    }
    
    /**
     * Extracts key/value pairs for an object
     */
    protected function getObjectVariables($object, $expected)
    {
        if (method_exists($object, 'toArray')) {
            $received = $object->toArray();
            foreach (array_intersect(array_keys($received), $expected) as $requiredParam) {
                $out[$requiredParam] = $received[$requiredParam];
            }
            
            return $out;
        }
        
        $out = array();
        
        foreach ($expected as $key) {
            if (method_exists($object, $method = 'get' . ucfirst($key))) {
                $out[$key] = $object->$method();
            }
        }
        
        return $out;
    }
}