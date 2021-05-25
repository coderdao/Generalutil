<?php


namespace Abo\Generalutil\V1\Utils;


class TimeUtil
{

    // 各时间单位 list( $season, $month, $week, $date ) = everyRangeTime();
    public function everyRangeTime( $date )
    {
        if ( !$date ) { $date = date( 'Y-m-d' ); }
        $timeStamp = strtotime( $date );

        return [
            $this->getSeason( $timeStamp ),
            $this->getMonth( $timeStamp ),
            $this->getWeek( $timeStamp ),
            $this->getDate( $timeStamp ),
        ];
    }

    // 第几季度
    protected function getSeason($uploadAt)
    {
        $yearMoth = date( 'Y', $uploadAt );

        $moth = date( 'm', $uploadAt );
        $season = ceil($moth/3);

        return "{$yearMoth}年-第{$season}季";
    }

    // 第几月
    protected function getMonth($uploadAt)
    {
        return date( 'Y-m', $uploadAt );
    }

    // 该月第几周
    protected function getWeek($uploadAt)
    {
        $yearMoth = date( 'm', $uploadAt );

        $day = date( 'd', $uploadAt );
        $week = ceil($day/7);

        return "{$yearMoth}月-第{$week}周";
    }

    // 第几日
    protected function getDate($uploadAt)
    {
        return date( 'Ymd', $uploadAt );
    }
}