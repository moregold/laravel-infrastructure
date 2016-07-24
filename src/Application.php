<?php namespace Moregold\Infrastructure;

use Illuminate\Support\Facades\Config,
    Illuminate\Foundation\Application as BaseApplication,
    Illuminate\Http\Request;

class Application extends BaseApplication
{
    /**
     * Throw an HttpException with the given data.
     *
     * @param  int     $code
     * @param  string  $message
     * @param  array   $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function abort($code, $message = '', array $headers = array())
    {
        if (!empty($message)) {
            $message_header = Config::get(
                'app.message_header', 'X-Moregold-Message'
            );
            $headers[$message_header] = $message;
        }
        parent::abort($code, $message, $headers);
    }
}
