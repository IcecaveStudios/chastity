<?php
namespace Icecave\Chastity\Driver\Redis;


/**
 * @access private
 *
 * Loads (and re-loads) a LUA script before use.
 */
interface LuaScriptInterface
{
    /**
     * Invoke the LUA script.
     *
     * @param mixed $argument,... The arguments to pass to the script.
     *
     * @return mixed The script result.
     */
    public function invoke();

    /**
     * Invoke the LUA script.
     *
     * @param array $arguments The arguments to pass to the script.
     *
     * @return mixed The script result.
     */
    public function invokeArray(array $arguments);

    /**
     * Get the SHA1 hash of the LUA source.
     *
     * @return string
     */
    public function hash();

    /**
     * Load the script immediately.
     */
    public function load();
}
