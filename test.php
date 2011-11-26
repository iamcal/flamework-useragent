<?
	#
	# to get common referers from an access log:
	# cat log | awk -F'(" "| "|" )' '{print $5}' | sort | uniq -c | sort -nr
	#
	#
	# to get a list of the top 100 UAs:
	# cat log | awk -F'(" "| "|" )' '{print $5}' | sort | uniq -c | sort -nr | head -n 100 | awk '{$1=""; print substr($0,2)}'
	#


	header('Content-type: text/plain');
	include('lib_useragent.php');


	$i = 1;
	$ua = null;
	$num_skipped = 0;
	$passed = 0;
	$total = 0;
	$show_passes = false;

	process_tests_file('tests/top-2011-11-19.tests');
	process_tests_file('tests/mobile.tests');
	summary();

	# lower the cache size so we can test cache eviction
	$GLOBALS['_useragent_cache_max'] = 100;
	process_agents_file('tests/first10k-2011-11-19.agents');


	function process_tests_file($test_file){

		echo "Processing tests from $test_file\n";

		$fh = fopen($test_file, 'r');
		if (!$fh) exit;
		while (($line = fgets($fh, 4096)) !== false){
			process_line($line);
		}
		if (!feof($fh)){
			echo "Error: unexpected fgets() fail\n";
			exit;
		}
		fclose($fh);
	}

	function process_line($line){

		global $ua, $num_skipped;

		if (preg_match('!^#!', $line)){

			return;

		}else if (preg_match('!^\t!', $line)){

			$line = trim($line);

			if (is_null($ua)){
				echo "found match line with no UA: $line\n";
				return;
			}

			$parts = preg_split('!\s+!', $line);

			run_test($ua, $parts);

			$ua = null;
		}else{
			if ($ua) $num_skipped++;

			$line = trim($line);
			if (strlen($line)){
				$ua = $line;
			}else{
				$ua = null;
			}
		}
	}

	function run_test($ua, $parts){

		global $i, $passed, $total;

		#echo "target: $ua\n";
		#echo "match: ";
		#print_r($parts);

		$map = array(
			0 => array('agent', 'agent_version'),
			1 => array('engine', 'engine_version'),
			2 => array('os', 'os_version'),
		);


		$ret = useragent_decode($ua);
		$pass = 0;

		do {

			foreach ($map as $k => $fields){

				if ($parts[$k] == '-'){

					if (!is_null($ret[$fields[0]])){
						echo "$i not ok\n";
						echo "# $ua\n";
						echo "# expecting blank $fields[0], got {$ret[$fields[0]]}\n";
						break 2;
					}

					if (!is_null($ret[$fields[1]])){
						echo "$i not ok\n";
						echo "# $ua\n";
						echo "# expecting blank $fields[1], got {$ret[$fields[1]]}\n";
						break 2;
					}

				}else if ($parts[$k]){

					list($a, $b) = explode('/', $parts[$k]);

					if ($ret[$fields[0]] != $a){
						echo "$i not ok\n";
						echo "# $ua\n";
						echo "# expecting $fields[0] $a, got {$ret[$fields[0]]}\n";
						break 2;
					}
					if ($ret[$fields[1]] != $b){
						echo "$i not ok\n";
						echo "# $ua\n";
						echo "# expecting $fields[1] $b, got {$ret[$fields[1]]}\n";
						break 2;
					}
				}
			}

			if ($GLOBALS['show_passes']){
				echo "$i ok\n";
				#print_r($ret);
			}

			$passed++;

		} while (0);

		$i++;
		$total++;
	}

	function summary(){

		global $num_skipped, $passed, $total;

		echo "\n";
		if ($num_skipped){
			echo "skipped $num_skipped agents - no results defined\n";
		}
		$per = round(1000 * $passed / $total) / 10;
		echo "Passed $passed of $total tests - $per%\n";
		echo "\n";
	}


	function process_agents_file($test_file){

		echo "Processing agents from $test_file\n";

		$keys = array('agent', 'engine', 'os');
		$map = array();

		$fh = fopen($test_file, 'r');
		if (!$fh) exit;
		while (($line = fgets($fh, 4096)) !== false){
		
			$line = trim($line);
			if (strlen($line)){

				$ret = useragent_decode_cached($line);

				foreach ($keys as $key){
					$map[$key][$ret[$key].'/'.$ret[$key.'_version']]++;
				}
			}
		}
		if (!feof($fh)){
			echo "Error: unexpected fgets() fail\n";
			exit;
		}
		fclose($fh);

		foreach ($keys as $key){
			$num = count($map[$key]);
			asort($map[$key]);
			$top = array_pop(array_keys($map[$key]));

			echo " - $num unique $key values (top: $top)\n";
		}
		echo "\n";

		#print_r($map);
	}
