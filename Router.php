<?php

/**
 * Class Router - Simple routing implementation
 */

class Router
{
    /**
     * @param null $filename list of routes, assumed config.ini in working directory if filename not provided
     * @return array of routes consisting of regex to match the route, controller class to load,
     * controller method to call and names of route parameters
     * @throws Exception if cannot open route file
     */
    public static function getConfigFileRoutes($filename = null)
    {
        $routes = [];
        if (empty($filename)) {
            $filename = './config.ini';
        }
        $fw = fopen($filename, 'r');
        if (false === $fw) {
            throw new Exception('Config file not found');
        }

        //parsing route file
        $foundRoutes = false;
        while ($line = fgets($fw)) {
            $line = trim($line);
            //ignore input until '[routes]' detected marking route section of config
            if (!$foundRoutes) {
                if ($line == '[routes]') {
                    $foundRoutes = true;
                }
            } else {

                //separate allowed HTTP method portion, route portion, and controller->method portion of line
                preg_match('/(\S+)\s+(\S+)\s+(\S+)/', $line,$matches);

                //parse url portion generating regex for matching route and param list
                $urlParse = self::parseRoute($matches[2]);

                //if not four matches, then config file format was not followed, so skip
                if (count($matches) == 4) {
                    //separate Class and Method to call
                    $callParts = explode('->', $matches[3]);
                    $urlParse['class'] = $callParts[0];
                    $urlParse['method'] = $callParts[1];
                    //split allowed HTTP methods into array
                    $urlParse['methodsHTTP'] = explode('|', $matches[1]);
                    //the raw unparsed route, for debuggin purposes
                    $urlParse['route'] = $matches[2];
                    //add route to list
                    $routes[] = $urlParse;
                }
            }
        }
        fclose($fw);
        return($routes);
    }

    /**
     * parseRoute parses a url segment to generate a regex for matching requests to routes
     * routes parameters are indicated by @[paramName]
     * @param $urlSegment
     * @return array contains the regex for matching this route and a subarray of the parameter names
     */
    public static function parseRoute($urlSegment) {
        $regEx = '/';
        $paramName = '';
        $urlLength = strlen($urlSegment);
        $parsingParam = false;
        $route = ['params' => []];

        //step through route string generating matching regex for static portions of route
        //and creating capturing groups for parameters
        for ($i = 0; $i < $urlLength; $i++) {
            if (!$parsingParam && $urlSegment[$i] != '@') {
                if ($urlSegment[$i] == '/') {
                    $regEx .= '\\';
                }
                $regEx .= $urlSegment[$i];
            } elseif (!$parsingParam) {
                $parsingParam = true;
            } elseif ($urlSegment[$i] == '/') {
                $route['params'][] = $paramName;
                $paramName = '';
                $regEx .= '([^\s\/]+)\\/';
                $parsingParam = false;
            } else {
                $paramName .= $urlSegment[$i];
            }
        }
        if ($parsingParam) {
            $route['params'][] = $paramName;
            $regEx .= '([^\s\/]+)$';
        } else {
            $regEx .= '$';
        }
        $regEx .= '/';
        $route['regex'] = $regEx;

        return $route;
    }

    /**
     * takes a route structure, loads the class and invokes controller method
     * in that route with input array of params
     * Controller class is assumed to be in a directory named 'controllers in CWD
     * @param $route        the route array structure
     * @param $paramsIn     the parameters to pass to the controller method
     * @return mixed        whatever the return value was from the controller method
     * @throws ReflectionException
     */
    public static function invokeRoute($route, $params)
    {
        require_once('./controllers/' . $route['class'] . '.php');
        $paramsOut = [];
        $i=0;
        $class = new ReflectionClass('Controller\\'.$route['class']);
        $instance = $class->newInstanceArgs();
        $method = $class->getMethod($route['method']);

        return $method->invokeArgs($instance, $params);
    }
}
