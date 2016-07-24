<?php namespace Moregold\Infrastructure;

use Illuminate\Console\Command as IlluminateCommand;
use Illuminate\Support\Facades\App;

abstract class Command extends IlluminateCommand
{
    /**
     * Determines if current job should send email or not
     *
     * @var boolean
     */
    public $send_email = true;

    /**
     * Timestamp of start log
     *
     * @var int
     */
    protected $startTime = null;

    /**
     * Count of successes
     *
     * @var integer
     */
    protected $success_count = 0;

    /**
     * Count of failures
     *
     * @var integer
     */
    protected $fail_count = 0;

    /**
     * Count of errors
     *
     * @var integer
     */
    protected $error_count = 0;

    /**
     * Convert time to human-friendly time
     *
     * @param int $time timestamp to convert
     *
     * @return string
     */
    protected function humanTiming($time)
    {
        $time = time() - $time;
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) {
                continue;
            }
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
        return $time.' seconds';
    }

    /**
     * Log the start of a job and send email notification if warranted
     *
     * @param string $comment optional comment string to include in start log
     *
     * @return void
     */
    public function start($comment = null)
    {
        $this->startTime = strtotime('now');
        if (empty($comment)) {
            $comment = 'Started '.get_class($this).' command.';
        }
        $this->comment($comment);
        if ($this->send_email) {
            $this->sendEmailStatus($comment);
        }
    }
    /**
     * Log the end of a job and send email notification if warranted
     *
     * @param string $comment    optional comment string to include in end log
     * @param string $attachment optional attachment to include in end log
     *
     * @return void
     */
    public function end($comment = null, $attachment = '')
    {
        $this->endTime = strtotime('now');
        if (empty($comment)) {
            $comment = 'Completed '.get_class($this).' command. '.$this->success_count.' succeeded, '.$this->fail_count.' failed, '.$this->error_count.' errors.';
        }
        $comment .= $this->getDuration();
        $this->comment($comment);
        if ($this->send_email) {
            $this->sendEmailStatus($comment."\n\n".$attachment);
        }
    }

    /**
     * Increment success count
     *
     * @return void
     */
    public function addSuccess($successes = 1)
    {
        $this->success_count += $successes;
    }

    /**
     * Increment failure count
     *
     * @return void
     */
    public function addFailure($failures = 1)
    {
        $this->fail_count += $failures;
    }

    /**
     * Increment error count
     *
     * @return void
     */
    public function addError($errors = 1)
    {
        $this->error_count += $errors;
    }

    /**
     * Call parent line method with class specific timestamp prefix
     *
     * @param string $string message for line log
     *
     * @return void
     */
    public function line($string, $style = null, $verbosity = null)
    {
        parent::line($this->getPrefix().$string, $style, $verbosity);
    }

    /**
     * Call parent info method with class specific timestamp prefix
     *
     * @param string $string message for info log
     *
     * @return void
     */
    public function info($string, $verbosity = null)
    {
        parent::info($this->getPrefix().$string, $verbosity);
    }

    /**
     * Call parent comment method with class specific timestamp prefix
     *
     * @param string $string message for comment log
     *
     * @return void
     */
    public function comment($string, $verbosity = null)
    {
        parent::comment($this->getPrefix().$string, $verbosity);
    }

    /**
     * Call parent error method with class specific timestamp prefix
     *
     * @param string $string message for error log
     *
     * @return void
     */
    public function error($string, $verbosity = null)
    {
        parent::error($this->getPrefix().$string, $verbosity);
    }

    /**
     * Determine if message needs to be sent, send if so
     *
     * @param string $message_text optional message text for email
     *
     * @return void
     */
    protected function sendEmailStatus($message_text = null)
    {
        if (!empty($message_text)) {
            $message_text = $this->getPrefix().$message_text;
        }
    }

    /**
     * Calculate the time difference from start to end and return human friendly time
     *
     * @return string
     */
    private function getDuration()
    {
        if (!is_null($this->startTime)) {
            return ' '.$this->humanTiming($this->startTime).' duration.';
        }
        return '';
    }

    /**
     * Generate prefix for job from current time
     *
     * @return string
     */
    private function getPrefix()
    {
        return $this->getTimeStamp().' ['.$this->getEnvironment().'] ';
    }

    /**
     * Determine environment
     *
     * @return string
     */
    private function getEnvironment()
    {
        return App::environment();
    }

    /**
     * Create timestamp from current time
     *
     * @return string
     */
    private function getTimeStamp()
    {
        return date('Y-m-d H:i:s', strtotime('now'));
    }
}
