<?
	function useragent_decode($ua){

		#
		# a list of user agents, in order we'll match them.
		# e.g. we put chrome before safari because chrome also
		# claims it is safari (but the reverse is not true)
		#

		$agents = array(
			'chrome',
			'safari',
			'konqueror',
			'firefox',
			'netscape',
			'opera',
			'msie',
		);

		$engines = array(
			'webkit',
			'gecko',
			'trident',
			'presto',
		);

		$ua = StrToLower($ua);
		$out = array();

		$temp = useragent_match($ua, $agents);
		$out['agent']		= $temp['token'];
		$out['agent_version']	= $temp['version'];

		$temp = useragent_match($ua, $engines);
		$out['engine']		= $temp['token'];
		$out['engine_version']	= $temp['version'];


		#
		# safari does something super annoying, putting the version in the
		# wrong place like: "Version/5.0.1 Safari/533.17.8"
		#
		# opera does the same thing:
		# http://dev.opera.com/articles/view/opera-ua-string-changes/
		#

		if ($out['agent'] == 'safari' || $out['agent'] == 'opera'){
			$temp = useragent_match($ua, array('version'));
			if ($temp['token']) $out['agent_version'] = $temp['version'];
		}


		#
		# OS matching needs to do some regex transformations
		#

		$os = array(
			'windows nt 5.1'		=> array('windows', 'xp'),
			'windows nt 5.2'		=> array('windows', 'xp x64'),
			'windows nt 6.0'		=> array('windows', 'vista'),
			'windows nt 6.1'		=> array('windows', '7'),
			'linux i686'			=> array('linux', 'i686'),
			'linux x86_64'			=> array('linux', 'x86_64'),
		);

		$out['os']		= null;
		$out['os_version']	= null;

		foreach ($os as $k => $v){
			if (strpos($ua, $k) !== false){
				$out['os'] = $v[0];
				$out['os_version'] = $v[1];
				break;
			}
		}

		if (is_null($out['os'])){
			do {
				if (preg_match('!mac os x (\d+)[._](\d+)([._](\d+))?!', $ua, $m)){
					$out['os'] = 'osx';
					$out['os_version'] = "$m[1].$m[2]";
					if ($m[4]) $out['os_version'] .= ".$m[4]";
					break;
				}

			} while (0);
		}

		return $out;
	}

	function useragent_match($ua, $tokens){

		foreach ($tokens as $token){

			if (preg_match("!{$token}[/ ]([0-9.]+)!", $ua, $m)){
				return array(
					'token'		=> $token,
					'version'	=> $m[1],
				);
			}

			if (preg_match("!$token!", $ua)){
				return array(
					'token'		=> $token,
					'version'	=> $null,
				);
			}
		}

		return array(
			'token'		=> null,
			'version'	=> null,
		);
	}
?>
