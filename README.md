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

* <code>chrome</code>
* <code>safari</code>
* <code>firefox</code>
* <code>msie</code>
* <code>dalvik</code> (android VM, not actually the browser)
* <code>opera</code>
* <code>netscape</code>
* <code>konqueror</code>
* <code>blackberry</code> (pre-safari)

<code>engine</code> contains the rendering engine,  with the full version number in <code>engine_version</code>:

* <code>webkit</code>
* <code>gecko</code>
* <code>trident</code>
* <code>presto</code>

<code>os</code> and <code>os_version</code> contain the platform, with the following values:

* <code>osx/n.n(.n)</code>
* <code>iphone/n.n(.n)</code>
* <code>ipad/n.n(.n)</code>
* <code>ipod/n.n(.n)</code>
* <code>windows/xp</code>
* <code>windows/xp-x64</code>
* <code>windows/vista</code>
* <code>windows/7</code>
* <code>linux/i686</code>
* <code>linux/x86_64</code>
* <code>android/n.n(.n)</code>
* <code>blackberry/$model</code>

When a field does not match one of the known values, it will be set to null.


Caveats
-------

* Not all browsers, rendering engines and platforms are supported - this is by design! Only common ones.
* We just return 'linux/i686' rather than e.g. 'Ubuntu', since most linux falvors don't give distro, plus nobody cares.
* Chromium returns 'chrome' - same thing anyway, plus it has a different version number.
* We differentiate iPad, iPhone and iPod, even though they are the same OS. You can patch this if you don't need to know.
