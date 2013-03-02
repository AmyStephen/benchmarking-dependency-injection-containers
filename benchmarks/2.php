<?php

echo PHP_EOL . PHP_EOL;

require __DIR__ . '/../vendor/autoload.php';

// To stop autoloader caching skewing results
$bart = new Benchmark\Stubs\Bart;
$bam = new Benchmark\Stubs\Bam($bart);
$baz = new Benchmark\Stubs\Baz($bam);
$bar = new Benchmark\Stubs\Bar($baz);
$foo =  new Benchmark\Stubs\Foo($bar);

unset($foo);
unset($bar);
unset($baz);
unset($bam);
unset($bart);

$bm = new Benchmark\Timer;

/*******************************************************************************
 Benchmark 2: Auto resolution of object and dependencies.
 (Register all objects with container)
 Excluded: Pimple, Symfony
********************************************************************************/

// Illuminate\Container (Laravel)
$bm->start('benchmark2', 'laravel');
$illuminate = new Illuminate\Container\Container;
$illuminate->bind('Foo', 'Benchmark\Stubs\Foo');
$illuminate->bind('Benchmark\Stubs\Bar');
$illuminate->bind('Benchmark\Stubs\Bam');
$illuminate->bind('Benchmark\Stubs\BazInterface', 'Benchmark\Stubs\Baz');
$illuminate->bind('Benchmark\Stubs\BartInterface', 'Benchmark\Stubs\Bart');
$foo = $illuminate->make('Foo');
$bm->end('benchmark2', 'laravel');
unset($illuminate);
unset($foo);

// Orno\Di
$bm->start('benchmark2', 'orno');
$orno = (new Orno\Di\Container)->autoResolve(true);
$orno->register('Benchmark\Stubs\Foo');
$orno->register('Benchmark\Stubs\Bar');
$orno->register('Benchmark\Stubs\Bam');
$orno->register('Benchmark\Stubs\BazInterface', 'Benchmark\Stubs\Baz');
$orno->register('Benchmark\Stubs\BartInterface', 'Benchmark\Stubs\Bart');
$foo = $orno->resolve('Benchmark\Stubs\Foo');
$bm->end('benchmark2', 'orno');
unset($orno);
unset($foo);

// Zend\Di
$bm->start('benchmark2', 'zend');
$zend = new Zend\Di\Di;
$zend->instanceManager()->addTypePreference('Benchmark\Stubs\Foo', 'Benchmark\Stubs\Foo');
$zend->instanceManager()->addTypePreference('Benchmark\Stubs\Bar', 'Benchmark\Stubs\Bar');
$zend->instanceManager()->addTypePreference('Benchmark\Stubs\Bam', 'Benchmark\Stubs\Bam');
$zend->instanceManager()->addTypePreference('Benchmark\Stubs\BazInterface', 'Benchmark\Stubs\Baz');
$zend->instanceManager()->addTypePreference('Benchmark\Stubs\BartInterface', 'Benchmark\Stubs\Bart');
$foo = $zend->get('Benchmark\Stubs\Foo');
$bm->end('benchmark2', 'zend');
unset($zend);
unset($foo);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Benchmark 2</title>

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
            ['Illuminate\\Container (Laravel)', <?= $bm->getBenchmarkData('benchmark2')['laravel']['time'][0] ?>],
            ['Orno\\Di', <?= $bm->getBenchmarkData('benchmark2')['orno']['time'][0] ?>],
            ['Zend\\Di', <?= $bm->getBenchmarkData('benchmark2')['zend']['time'][0] ?>]
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
