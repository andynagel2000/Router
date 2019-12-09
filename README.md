This project implements a simple routing framework.

It was tested on Nginx running PHP 7.2.11 and php-fpm on a clean Centos 8.

A copy of bash commands for set up and the simple Nginx config file
for directing all requests to index.php has been provided.

But this should run on any PHP 7.2/Web server as as long as index.php, Router.php, config.ini, 
and the controller folder are at the root of PHP's working directory and the server is
configured to direct all requests to index.php.

The Router class is called to read in all routes from config.ini (if custom filename not provided).  
Te file format is largely based on how Fat-Free framework does it, and requires 
a [routes] section where the routes are each on a line with allowed HTTP methods, 
url segment, and Controller Class and method name.

Example

POST|PATCH		/thing/update/@param		ControllerClassName->methodName

Parsing of routes generates an array with an entry for each route that 
includes a regex future requests are matched against as well as 
a list of parameter names (which were used for debugging only, the regex is what matters)

At that point request uri segments are checked against the regex in the route array, and on a match,
calls the Router class to invoke the Controller and method with the parameters parsed from uri segment.

Otherwise 404, 405, or 500 Headers are sent as appropriate

Please note the [routes] is required, I had though initially I might put other sections
into this config file and get fancier.  But I ran short on time and was getting 
into things that weren't really part of the task.

To further critique my own work:

The array of info parsed for each route should have been put into its own Class
so the structure was clear and well documented.  And then the logic for matching a URI segment
could have been included as a class method.  This would have lent itself better to unit testing, 
which I did not include.

And in general, I could have been more careful with namespacing and error handling.

Also in the real world, I would certainly cache the results of initially parsing the
route config file so it wouldn't need to be done on every request.  But, I didn't want to complicate configuration, try and keep this easy to run.
