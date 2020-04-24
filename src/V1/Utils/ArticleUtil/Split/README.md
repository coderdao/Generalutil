# 文件列表：
- demo.php 演示程序
- dict_build.php 词典编译程序
- SplitWordUtil .php 核心类
- /dict 词典文件

# 使用环境：
PHP >= PHP5.0

# 简单例子
# phpanalysis
基于phpanalysis的功能封装,方便使用php语言分词

```
    \Abo\Generalutil\V1\Utils\ArticleUtil\Split\SplitWordUtil::$loadInit = false;
    $pa = new \Abo\Generalutil\V1\Utils\ArticleUtil\Split\SplitWordUtil('utf-8', 'utf-8', true);
    //载入词典
    $pa->LoadDict();
    //执行分词
    $pa->SetSource('今天不要忘记给智聪带面膜
    昨天喝了咖啡，晚上失眠，现在蓝瘦香菇
    北京天气变得好冷了，秋风萧瑟');
    $pa->differMax = true;
    $pa->unitWord = true;
    $pa->StartAnalysis( true );
    $okresult = $pa->GetFinallyResult('###', false);

    print_r($okresult);
```
