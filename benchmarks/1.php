<?php

echo PHP_EOL . PHP_EOL;

require __DIR__ . '/../vendor/autoload.php';

$bm = new Benchmark\Timer;

/*******************************************************************************
 Benchmark 1: Auto resolution of object and dependencies.
 (Aliasing Interfaces to Concretes)
 Excluded: Pimple, Symfony
********************************************************************************/

// Illuminate\Container (Laravel)
$bm->start('benchmark1', 'laravel');
$illuminate = new Illuminate\Container\Container;
$illuminate->bind('Foo', 'Benchmark\Stubs\Foo');
$illuminate->bind('Benchmark\Stubs\BazInterface', 'Benchmark\Stubs\Baz');
$illuminate->bind('Benchmark\Stubs\BartInterface', 'Benchmark\Stubs\Bart');
$foo = $illuminate->make('Foo');
$bm->end('benchmark1', 'laravel');
unset($illuminate);
unset($foo);

// Orno\Di
$bm->start('benchmark1', 'orno');
$orno = (new Orno\Di\Container)->autoResolve(true);
$orno->register('Benchmark\Stubs\BazInterface', 'Benchmark\Stubs\Baz');
$orno->register('Benchmark\Stubs\BartInterface', 'Benchmark\Stubs\Bart');
$foo = $orno->resolve('Benchmark\Stubs\Foo');
$bm->end('benchmark1', 'orno');
unset($orno);
unset($foo);

// Zend\Di
$bm->start('benchmark1', 'zend');
$zend = new Zend\Di\Di;
$zend->instanceManager()->addTypePreference('Benchmark\Stubs\BazInterface', 'Benchmark\Stubs\Baz');
$zend->instanceManager()->addTypePreference('Benchmark\Stubs\BartInterface', 'Benchmark\Stubs\Bart');
$foo = $zend->get('Benchmark\Stubs\Foo');
$bm->end('benchmark1', 'zend');
unset($zend);
unset($foo);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Benchmark 5</title>

    <meta name="viewport" content="width-device-width, initial-scale=1">
</head>
<body>
    <div id="chart_div" style="width: 800px; height: 500px;"></div>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Component', 'Time Taken'],
            ['Illuminate\\Container (Laravel)', <?= $bm->getBenchmarkData('benchmark1')['laravel']['time'][0] ?>],
            ['Orno\\Di', <?= $bm->getBenchmarkData('benchmark1')['orno']['time'][0] ?>],
            ['Zend\\Di', <?= $bm->getBenchmarkData('benchmark1')['zend']['time'][0] ?>]
        ]);

        var options = {
            hAxis: {title: 'Component', titleTextStyle: {color: 'red'}},
            vAxis: {title: 'Time Taken (Seconds)', titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
    </script>
</body>
</html>