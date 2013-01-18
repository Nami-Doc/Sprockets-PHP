<?php
namespace Asset\Filter;

class Styl extends Base
{
	public function __invoke($content, $file, $dir, $vars)
	{
		$cache_file = $this->getCacheDir($file . '_' . $md5 = md5($content) . '|styl', __CLASS__);

		$this->registerFile($file, $css_cache_file = str_replace('.styl', '.css', $cache_file));

		if (file_exists($css_cache_file))
			return file_get_contents($css_cache_file);

		if (!file_exists($cache_file))
			file_put_contents($cache_file, $content);

		$nib = NODE_MODULES_PATH . 'nib/lib/nib';
		$out = $this->processNode(array('stylus/bin/stylus', '-u', $nib, $cache_file));

		if (!file_exists($css_cache_file))
		{
			echo "Stylus Compilation Error<pre>" . str_replace($cache_file, $file, $out) . "</pre>";

			@unlink($log);
			exit;
		}

		return file_get_contents($css_cache_file);
	}
}