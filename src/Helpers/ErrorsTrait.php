<?php namespace Moregold\Infrastructure\Helpers;

use \Exception;

trait ErrorsTrait
{
    /**
     * Currently handled error, if any
     *
     * @var Exception
     */
    private $current_error = null;

    /**
     * Detect if error-free
     *
     * @return boolean Error-free
     */
    public function success()
    {
        return is_null($this->current_error);
    }

    /**
     * Detect if has error
     *
     * @return boolean Error found
     */
    public function failure()
    {
        return !is_null($this->current_error);
    }

    /**
     * Log and store the error
     *
     * @return mixed
     */
    public function logError(Exception $e)
    {
        $this->current_error = $e;
        return $this;
    }

    /**
     * Return the error
     *
     * @return Exception|null Exception, if available
     */
    public function getError()
    {
        return $this->current_error;
    }
}
