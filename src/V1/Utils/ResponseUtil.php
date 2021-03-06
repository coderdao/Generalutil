<?php

namespace Abo\Generalutil\V1\Utils;

use Illuminate\Support\Collection;

class ResponseUtil
{
    protected $isSuccess;
    protected $message = '';
    protected $data = [];

    protected $page = 1;
    protected $pageSize = 0;
    protected $totalNums = 0;
    protected $totalPage = 1;

    protected $jump = '';

    public function __construct(bool $isSuccess)
    {
        $this->isSuccess = $isSuccess;
    }

    public static function success()
    {
        return new static(true);
    }

    public static function error()
    {
        return new static(false);
    }

    public function message(string $data)
    {
        $this->message = $data;
        return $this;
    }

    public function data($data)
    {

        if (is_array($data)) {
            $this->data = $data;
        } elseif ($data instanceof Collection) {
            $this->data = $data->toArray();
        } elseif (self::isJson($data)) {
            $this->data = json_decode($data);
        } elseif (is_string($data)) {
            $this->data = $data;
        } else {
            throw new \Exception('Server Api Response Param Unexpected.');
        }
        return $this;
    }

    public function jump(string $data)
    {
        $this->jump = $data;
        return $this;
    }

    public function paginate( int $totalNums, int $pageSize, int $page = 1 )
    {
        $page <= 1 && $page = 1;
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->totalNums = $totalNums;

        if ( !$totalNums || !$pageSize ) {
            return $this;
        }

        $this->totalPage = ceil( $totalNums / $pageSize );
        return $this;
    }

    /**
     * 通过JSONP返回数据
     *
     * @param array $append
     * @param string $callbackKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonp($append = [], $callbackKey = 'jsoncallback')
    {
        $callback = request()->get($callbackKey) ?: 'callback';

        $ret = [
            'status' => $this->isSuccess,
            'msg' => $this->message,
            'jump' => $this->jump,
            'data' => $this->data,
        ];
         if (! empty($append)) {
             $ret = array_merge($ret, $append);
         }

        return response()->jsonp($callback, $ret);
    }

    protected static function isJson($data)
    {
        \json_decode($data);
        if (\json_last_error()) {
            return false;
        }

        return true;
    }


    /**
     * 直接返回json数据
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function json()
    {
        return response()->json([
            'status' => $this->isSuccess,
            'msg' => $this->message,
            'data' => $this->data,
            'jump' => $this->jump,
            'paginate' => [
                'page' => $this->page,
                'page_size' => $this->pageSize,
                'total_page' => $this->totalPage,
                'total_num' => $this->totalNums,
            ],
        ]);
    }
}