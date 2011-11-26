A Flamework library for decoding User Agent strings
===================================================

This library is designed to be used with <a href="https://github.com/exflickr/flamework">Flamework</a>, but works standalone too.

Usage:

  # single shot
  $ret = useragent_decode($ua);

  # when decoding in batches
  $ret = useragent_decode_cached($ua);

  # return structure
  $ret = array(
    'agent'		=> 'chrome',
    'agent_version'	=> '15.0.874.121',
    'engine'		=> 'webkit',
    'engine_version'	=> '535.2',
    'os'		=> 'osx',
    'os_version'	=> '10.7.2',
  );


Possible values
---------------
  
<code>agent</code> will be one of the following, with the full version number in <code>agent_version</code>:
* chrome
* safari
* firefox
* msie
* opera
* netscape
* konqueror

<code>engine</code> contains the rendering engine,  with the full version number in <code>engine_version</code>::
* webkit
* gecko
* trident
* presto

<code>os</code> and <code>os_version</code> contain the platform, with the following values:
* osx/n.n
* osx/n.n.n
* windows/xp
* windows/xp-x64
* windows/vista
* windows/7
* linux/i686
* linux/x86_64


Caveats
-------

* Not all browsers, rendering engines and platforms are supported - this is by design! Only common ones.
* We just return 'linux/i686' rather than e.g. 'Ubuntu', since most linux falvors don't give distro, plus nobody cares.
* Chromium returns 'chrome' - same thing anyway, plus it has a different version number.
