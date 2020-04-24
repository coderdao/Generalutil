<?php
/**
 * 余弦文字相似计算
 * 解决方案是首先进行文章分词可以用结巴或者迅搜分词服务进行文章分词,然后将需要对比的文章分词结果存入redis，
 * 在有新文章进行对比的时候从redis将所有文章的分词结果从内存中取出来然后进行相似度对比，逐词进行相似度计算。
 *
 * 相似度计算的准确性很高，但是对比的文章量非常大的时候,处理时间还是会很长,5000文章的相似度计算需要近 30s
 */

namespace Abo\Generalutil\V1\Utils\ArticleUtil\Similar;


use Abo\Generalutil\V1\Utils\ArticleUtil\Split\SplitWordUtil;

class SimilarCosineUtil
{
    /**
     * 排除的词语
     *
     * @var array
     */
    private $_excludeArr = array('的', '了', '和', '呢', '啊', '哦', '恩', '嗯', '吧');

    /**
     * 词语分布数组
     *
     * @var array
     */
    private $_words = array();

    /**
     * 分词后的数组一
     *
     * @var array
     */
    private $_segList1 = array();

    /**
     * 分词后的数组二
     *
     * @var array
     */
    private $_segList2 = array();

    private $test1 = array();
    private $test2 = array();

    /**
     * 分词两段文字 计算相似度
     *
     * @param type $text1 description
     * @param type $text2 description
     * @return type description
     */
    public function run($text1, $text2)
    {
        $this->_segList1 = is_array( $text1 ) ? $text1 : $this->segment( $text1 );
        $this->_segList2 = is_array( $text2 ) ? $text2 : $this->segment( $text2 );

        $this->analyse();
        $rate = $this->handle();
        return $rate ? $rate : 'errors';
    }

    /**
     * 分析两段文字
     */
    private function analyse()
    {
        //t1
        foreach ($this->_segList1 as $v) {
            if (!in_array($v, $this->_excludeArr)) {
                if (!array_key_exists($v, $this->_words)) {
                    $this->_words[$v] = array(1, 0);
                } else {
                    $this->_words[$v][0] += 1;
                }
            }
        }

        //t2
        foreach ($this->_segList2 as $v) {
            if (!in_array($v, $this->_excludeArr)) {
                if (!array_key_exists($v, $this->_words)) {
                    $this->_words[$v] = array(0, 1);
                } else {
                    $this->_words[$v][1] += 1;
                }
            }
        }
    }

    /**
     * 处理相似度
     *
     * @return type description
     */
    private function handle()
    {
        $sum = $sumT1 = $sumT2 = 0;
        foreach ($this->_words as $word) {
            $sum += $word[0] * $word[1];
            $sumT1 += pow($word[0], 2);
            $sumT2 += pow($word[1], 2);
        }

        $rate = $sum / (sqrt($sumT1 * $sumT2));
        return $rate;
    }

    /**
     * 分词
     *
     * @param string $text description
     *
     * @return mixed description
     *
     * @description 分词只是一个简单的例子，你可以使用任意的分词服务
     */
    private function segment( $work )
    {
        SplitWordUtil::$loadInit=false;
        $pa = new SplitWordUtil('utf-8', 'utf-8', true);
        //载入词典
        $pa->LoadDict();
        //执行分词
        $pa->SetSource( $work );
        $pa->differMax = true;
        $pa->unitWord = true;
        $pa->StartAnalysis( true );
        $okresult = $pa->GetFinallyResult(',', false);
        return $okresult;
    }
}

// $TextSimilarity = new SimilarCosineUtil();
// var_dump( $TextSimilarity->run( "吉林禽业公司火灾已致112人遇难","吉林宝源丰禽业公司火灾已致112人遇难" ) );