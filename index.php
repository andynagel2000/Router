<?php

/**
 * This file serves as entry point for an extremely simple routing framework
 * all HTTP requests are directed to this file which invokes the Router class
 * to parse a routing config file into corresponding regex
 * current REQUEST_URI is matched against the route regex and invoke
 * appropriate Class method with params
 *
 * The routes in config file are the form of
 * * allowed HTTP methods in caps separated by |
 * * the url segment with dynamic parameters indicated by an @ in front of the param name
 * * Controller and method to invoke in the form ClassName->methodName
 */

require_once './Router.php';

$routes = Router::getConfigFileRoutes();
$routeFound = false;

foreach ($routes as $route) {
    preg_match($route['regex'], rtrim($_SERVER['REQUEST_URI'], '/'), $matches);

    //first check if match with route regex
    if (count($matches)-1 == count($route['params'])) {
        //matched regex, check if request method is allowed then invoke route
        if (in_array($_SERVER['REQUEST_METHOD'], $route['methodsHTTP'])) {
            $routeFound = true;
            array_shift($matches);
            try {
                Router::invokeRoute($route, $matches);
            } catch (ReflectionException $e) {
                header("500 Error loading controller " . $route['class'] . '->' , $route['method'], true, 500);
            }
            break;
        } else {
            //route was matched, but method not allowed
            header("405 Method Not Allowed", true, 405);
        }
    }
}

if (!$routeFound) {
    //route not found, generic 404
    header("404 Page Not Found", true, 404);
}
