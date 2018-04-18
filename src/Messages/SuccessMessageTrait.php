<?php namespace Moregold\Infrastructure\Messages;

use \Illuminate\Support\MessageBag;

trait SuccessMessageTrait
{
    /**
     * Collection of success messages
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $successes;

    /**
     * Assigns successes property as new MessageBag,
     * trait constructors are dangerous, this must be executed in constructor
     * of parent class
     */
    protected function initSuccessMessageAble()
    {
        if (@get_class($this->successes) != 'Illuminate\Support\MessageBag')  {
            $this->successes = new MessageBag();
        }
    }

    /**
     * Retrieve collection of successes
     *
     * @return \Illuminate\Support\MessageBag Collection of successes
     */
    public function getSuccesses()
    {
        $this->initSuccessMessageAble();
        return $this->successes;
    }

    /**
     * Builds string of successes in success collection
     *
     * @return string Concatenated list of all successes in success collection
     */
    public function getSuccessesAsString()
    {
        $this->initSuccessMessageAble();
        $success_string = preg_replace("/\.\,+/", ",", implode(', ', $this->successes->all()));
        return !empty($success_string) ? $success_string : null;
    }

    /**
     * Check is success collation contains messages
     *
     * @return boolean Success collection contains at least one message
     */
    public function hasSuccesses()
    {
        $this->initSuccessMessageAble();
        return !$this->successes->isEmpty();
    }

    /**
     * Add a success message to collection
     *
     * @param string $key     Name of message or message group type
     * @param string $message Body of message
     *
     * @return \stdClass Parent class
     */
    public function addSuccess($message, $key = null)
    {
        if (empty($key)) {
            $key = TypesFacade::defaultSuccess();
        }
        $this->initSuccessMessageAble();
        $this->successes->add($key, $message);
        return $this;
    }

    /**
     * Add collection of messages to collection
     *
     * @param array $messages Collection of messages to merge into collection
     *
     * @return \stdClass Parent class
     */
    public function addSuccesses($messages)
    {
        $this->initSuccessMessageAble();
        $this->successes->merge($messages);
        return $this;
    }
}
