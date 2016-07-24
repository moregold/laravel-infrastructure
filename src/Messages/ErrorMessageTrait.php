<?php namespace Moregold\Infrastructure\Messages;

use \Illuminate\Support\MessageBag;

trait ErrorMessageTrait
{
    /**
     * Collection of error messages
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    protected $statusCode;

    /**
     * Assigns errors property as new MessageBag,
     * trait constructors are dangerous, this must be executed in constructor
     * of parent class
     */
    protected function initErrorMessageAble()
    {
        if (get_class($this->errors) != 'Illuminate\Support\MessageBag')  {
            $this->errors = new MessageBag();
        }
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Retrieve collection of errors
     *
     * @return \Illuminate\Support\MessageBag Collection of errors
     */
    public function getErrors()
    {
        $this->initErrorMessageAble();
        return $this->errors;
    }

    /**
     * Builds string of errors in error collection
     *
     * @return string Concatenated list of all errors in error collection
     */
    public function getErrorsAsString()
    {
        $this->initErrorMessageAble();
        $error_string = preg_replace("/\.\,+/", ",", implode(', ', $this->errors->all()));
        return !empty($error_string) ? $error_string : null;
    }

    /**
     * Check is error collation contains messages
     *
     * @return boolean Error collection contains at least one message
     */
    public function hasErrors()
    {
        $this->initErrorMessageAble();
        return !$this->errors->isEmpty();
    }

    /**
     * Add a error message to collection
     *
     * @param string $key     Name of message or message group type
     * @param string $message Body of message
     *
     * @return \stdClass Parent class
     */
    public function addError($message = null, $key = null, $statusCode = 500)
    {
        if (empty($key)) {
            $key = TypesFacade::defaultError();
        }
        $this->initErrorMessageAble();
        $this->errors->add($key, $message);
        $this->setStatusCode($statusCode);
        return $this;
    }

    /**
     * Add collection of messages to collection
     *
     * @param array $messages Collection of messages to merge into collection
     *
     * @return stdClass Parent class
     */
    public function addErrors($messages, $statusCode = 500)
    {
        $this->initErrorMessageAble();
        $this->errors->merge($messages);
        $this->setStatusCode($statusCode);
        return $this;
    }

    /**
     * Instantiate new object, add error and return.
     *
     * @param  string $message Optional message to include
     *
     * @return \stdClass Parent class
     */
    public static function throwError($message = null)
    {
        $isbn = new static;
        return $isbn->addError($message);
    }
}
