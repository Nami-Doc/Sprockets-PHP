<?php
function vardump(){echo'<pre>';$e=func_get_args();foreach($e as $a)var_dump($a);}
function vdump(){call_user_func_array('vardump',func_get_args());exit;}
require __DIR__.'/../vendor/autoload.php';


// read paths.json - see below
// you can of course pass a normal array !
$paths = str_replace('%template%', 'MyTemplate', file_get_contents(__DIR__.'/paths.json'));
$paths = json_decode($paths, true);

# Change one test path to absolute:
$paths['template']['directories'][0] = __DIR__.'/'.$paths['template']['directories'][0];

class MeowFilter extends Sprockets\Filter\Base
{
	public function __invoke($content, $file, $dir, $vars)
	{
		return 'meow';
	}
}

// create a pipeline
$pipeline = new Sprockets\Pipeline($paths);
$pipeline->registerFilter('meow', 'MeowFilter');

function expect($type, $actual)
{
	$expect = str_replace("\r", '', trim(file_get_contents('_expect/expect.' . $type)));
	$actual = str_replace("\r", '', trim($actual));
	if ($expect == $actual)
		echo "$type : Test passed.<br/>";
	else
		echo "ERROR -- $type
<table>
	<thead>
		<tr>
			<th>Expected</th>
			<th>Got</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width='50%'><pre>$expect</pre></td>
			<td width='50%'><pre>$actual</pre></td>
		</tr>
	</tbody>
</table>
	Files are available in <pre>_results/<b>expect or got</b>.$type</pre>";
	if (!file_exists('_results'))
		mkdir('_results');
	file_put_contents('_results/expect.'.$type, $expect);
	file_put_contents('_results/got.'.$type, $actual);
}
expect('js', $pipeline('js'));
expect('css', $pipeline('css'));