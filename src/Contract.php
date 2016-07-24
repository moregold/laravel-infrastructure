<?php namespace Moregold\Infrastructure;

use \Exception;

class Contract
{
    /**
     * Count of all records associated with query
     *
     * @var integer
     */
    public $total_records = 0;

    /**
     * Count of records on the current query page
     *
     * @var integer
     */
    public $per_page = 0;

    /**
     * Number of records skipped in the query
     *
     * @var integer
     */
    public $skip = 0;

    /**
     * Number of records included in the query
     *
     * @var integer
     */
    public $take = 0;

    /**
     * Collection of result in the query
     *
     * @var array
     */
    public $records;

    /**
     * Optional plain text message to include
     *
     * @var string
     */
    public $message = null;

    /**
     * Phrase used in the query, if available
     *
     * @var string
     */
    public $phrase = null;

    /**
     * Magically delicious method to set properties with a bit of business logic
     *
     * @param  string                           $method Name of method requested
     * @param  array                            $args   Arguments provided
     *
     * @return \Moregold\Infrastructure\Contract           Modified contract
     */
    public function __call($method, $args)
    {
        if (property_exists($this, $method)) {
            $this->{$method} = $args[0];
            if ($method == 'per_page' || $method == 'total_records') {
                $this->buildMessage();
            }
        }
        return $this;
    }

    /**
     * Add error to contract
     *
     * @param  Exception                     $error Error
     *
     * @return \Moregold\Infrastructure\Contract       Modified contract
     */
    public function withError(Exception $error)
    {
        $this->message = $error->getMessage();
        return $this;
    }

    /**
     * get records
     * 
     * @return array
     */
    public function getRecords() {
        return $this->records;
    }

    /**
     * Update contract message based on current values
     *
     * @return void
     */
    private function buildMessage()
    {
        $this->message = $this->per_page . ' of ' . $this->total_records . ' result' . ($this->total_records == 1 ? '' : 's');
    }
}
