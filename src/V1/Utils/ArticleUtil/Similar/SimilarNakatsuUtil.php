<?php
/**
 * 相似度计算
 *
 * 原理
 * 线性空间求最长公共子序列的Nakatsu算法
 * https://www.cnblogs.com/grenet/archive/2011/03/11/1964417.html
 */

namespace Abo\Generalutil\V1\Utils\ArticleUtil\Similar;
class SimilarNakatsuUtil
{
    private $str1;
    private $str2;
    private $c = [];
    private $ignoreWord = [];
    /**
     * 返回两个串的相似度
     */
    public function getSimilar($str1, $str2) {
        if ( $this->ignoreWord ) {
            $str1 = str_replace( $this->ignoreWord, '', $str1 );
            $str2 = str_replace( $this->ignoreWord, '', $str2 );
        }

        $len1 = strlen($str1);
        $len2 = strlen($str2);

        $len = strlen($this->getLCS($str1, $str2, $len1, $len2));
        return $len * 2 / ($len1 + $len2);
    }

    // 设置忽略文字
    public function setIgnoreWord( array $word = [])
    {
        if ( $word ) {
            $this->avoidWord = $word;
        }

        return $this;
    }

    /**
     * 返回串一和串二的最长公共子序列
     * @param $str1
     * @param $str2
     * @param int $len1
     * @param int $len2
     * @return string
     */
    private function getLCS($str1, $str2, $len1 = 0, $len2 = 0) {
        $this->str1 = $str1;
        $this->str2 = $str2;

        if ($len1 == 0) $len1 = strlen($str1);
        if ($len2 == 0) $len2 = strlen($str2);

        $this->initC($len1, $len2);
        return $this->printLCS($this->c, $len1 - 1, $len2 - 1);
    }

    private function initC($len1, $len2) {
        for ($i = 0; $i < $len1; $i++) $this->c[$i][0] = 0;
        for ($j = 0; $j < $len2; $j++) $this->c[0][$j] = 0;
        for ($i = 1; $i < $len1; $i++) {
            for ($j = 1; $j < $len2; $j++) {
                if ($this->str1[$i] == $this->str2[$j]) {
                    $this->c[$i][$j] = $this->c[$i - 1][$j - 1] + 1;
                } else if ($this->c[$i - 1][$j] >= $this->c[$i][$j - 1]) {
                    $this->c[$i][$j] = $this->c[$i - 1][$j];
                } else {
                    $this->c[$i][$j] = $this->c[$i][$j - 1];
                }
            }
        }
    }

    private function printLCS($c, $i, $j) {
        if ($i == 0 || $j == 0) {
            if ($this->str1[$i] == $this->str2[$j]) {
                return $this->str2[$j];
            } else {
                return "";
            }
        }
        if ($this->str1[$i] == $this->str2[$j]) {
            return $this->printLCS($this->c, $i - 1, $j - 1).$this->str2[$j];
        } else if ($this->c[$i - 1][$j] >= $this->c[$i][$j - 1]) {
            return $this->printLCS($this->c, $i - 1, $j);
        } else {
            return $this->printLCS($this->c, $i, $j - 1);
        }
    }

}

//$lcs = new LCS();
//返回最长公共子序列
//echo $lcs->getLCS("hello word","hello china");
//echo "<br/>";
//返回相似度
//echo $lcs->getSimilar("吉林禽业公司火灾已致112人遇难","吉林宝源丰禽业公司火灾已致112人遇难" )."\r\n";
//
//echo similar_text("吉林禽业公司火灾已致112人遇难","吉林宝源丰禽业公司火灾已致112人遇难");