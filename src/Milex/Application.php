<?php
/**
 * @author msmith
 * @created 5/16/13 4:11 PM
 */

namespace Milex;

use Cilex\Application as BaseApplication;
use Cilex\Provider\Console\ConsoleServiceProvider;

class Application extends BaseApplication {

    /**
     * Registers the autoloader and necessary components.
     *
     * @param string      $name    Name for this application.
     * @param string|null $version Version number for this application.
     */
    public function __construct($name, $version = null, array $values = array())
    {
        $consoleConfig = array('console.class' => '\Milex\Adapter\ContainerAwareApplication', 'console.name' => $name);
        if (null !== $version) {
            $consoleConfig['console.version'] = $version;
        }
        $this->register(new ConsoleServiceProvider(), $consoleConfig);

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
    }
}
