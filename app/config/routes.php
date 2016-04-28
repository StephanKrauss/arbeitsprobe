<?php
/**
 * Bootstrap
 *
 * + Routing zu Controller / Action
 * + ermitteln Parameter
 * + Errorcontroller
 */

// Start Session
session_start();

// Aufruf Baustein 'start' , Erststart
\Flight::route('/', function()
{
    // Controller
    include_once('../src/Controller/start.php');
    $controller = new \controller\start('start', 'index');

    // Startroutinen des Controller
    startController($controller, 'index');
});

// Aufruf Baustein mit Controller / Action
\Flight::route('/@controller/@action(/*)',function($controller, $action)
{
    if($action == null)
        $action = 'index';

    // ermitteln der übergebenen Parameter
    $data = ermittelnStartParams();

    // ermitteln Pfade zu den CSV Dateien
    getCsvPath();

    // Controller
    $controllerString = "controller\\$controller";
    $controller = new $controllerString($controller, $action);
    
    // Startroutinen des Controller
    startController($controller, $action, $data);
});

// Mapping 'not found'
\Flight::map('notFound', function() {
    // \Flight::render('404', array());
    Flight::redirect('/start/index');
});

/**
* Start des Controller und der Action und Übernahme der Daten
*
* @param $controller
* @param $action
*/
function startController($controller, $action = 'index', $data = null)
{
    if( (is_array($data)) and (count($data) > 0) )
        $controller->setData($data);

    $controller->$action();
}

/**
 * ermitteln Startparameter einer Action eines Controller
 *
 * @return array
 */
function ermittelnStartParams()
{
    $request = \Flight::request();
    $params = array();

    if($request->method == 'POST'){
        $params = $_POST;
    }

    if($request->method == 'GET'){
        $url = $request->url;
        $url = ltrim($url,'/');
        $url = rtrim($url, '/');

        $splitUrl = explode('/',$url);

        if(isset($splitUrl[0]))
            unset($splitUrl[0]);
        if(isset($splitUrl[1]))
            unset($splitUrl[1]);

        $splitUrl = array_merge($splitUrl);

        $j=1;
        if(count($splitUrl) >= 2){

            $key = null;
            for($i = 0; $i < count($splitUrl); $i++){
                if($j % 2 == 0){
                    $params[$key] = $splitUrl[$i];
                    $key = null;
                }
                else{
                    $key = $splitUrl[$i];
                }

                $j++;
            }
        }
    }

    \Flight::set('params', $params);

    return;
}

/**
 * speichern der Pfade zu den CSV Dateien
 *
 * @return array
 */
function getCsvPath()
{
    include_once('../app/config/csvPaths.php');

    \Flight::set('csvPaths',$csvPaths);

    return $csvPath;
}