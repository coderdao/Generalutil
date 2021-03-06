<?php
/**
 * Function:
 * Description:
 * Abo 2019/2/15 11:02
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\V1\Utils;

class FileUtil
{

    /**
     * 获取文件类型
     */
    public static function getFileType($fileName)
    {
        $src = explode(".",$fileName);

        $fileType = $src[sizeof($src)-1];
        $fileName = rtrim( $fileName, ".{$fileType}" );
        return [ $fileName, $fileType ];
    }

    public static function mkdir($path)
    {
        if (strpos($path, dirname(PATH_APP)) !== false && strpos($path, '/work/files') !== false) {
            throw new \Exception('path disabled', 108);
        }
        $p = array();
        foreach (explode('/', $path) as $d) {
            $p[] = $d;
            if (!is_dir("/" . implode('/', $p))) {
                if (is_file("/" . implode('/', $p))) {
                    throw new \Exception('path file name exist', 109);
                }
                mkdir("/" . implode('/', $p), 0777);
                chmod("/" . implode('/', $p), 0777);
            }
        }
        return true;
    }

    /** 检查文件目录是否存在 目录无则创建 文件有则删除 */
    public static function checkDirFileExist( string $filePath )
    {
        $dirPath = dirname( $filePath );
        if ( !$dirPath ) { throw new \Exception( 500, '系统异常：暂无目录信息' ); }

        //目录不存在则创建目录
        if ( !is_dir( $dirPath ) && !mkdir( $dirPath, 755,true ) ) {
            throw new \Exception( 500, '系统异常：目录创建失败' );//mkdir没加true是上一级目录不存在就会创建失败
        }

        //生成文件存在 则删除，重新创建
        if (file_exists( $filePath )) {
            unlink( $filePath );
        }

        return true;
    }

    /** 文件重命名 */
    public static function rename( string $oldFilePath, string $newFilePath )
    {
        if ( !$newFilePath )
            { throw new \Exception( '500', '新文件名不能为空:'.$newFilePath ); }
        if ( !$oldFilePath || !file_exists( $oldFilePath ) )
            { throw new \Exception( '500', '重命名文件不存在:'.$oldFilePath ); }
        if ( file_exists( $newFilePath ) ) { return '新文件已存在'; }

        return rename( $oldFilePath, $newFilePath );
    }


    /**
     * 下载文件到目标路径
     * @param $fileMd5  string 文件MD5
     * @param $targetFilePath  string 保存文件路径
     * @return bool
     * @throws \Exception
     */
    public function downloadOneFile2Target( $sourceUrl, $targetFilePath, $mod = 777 )
    {
        set_time_limit( 0 );
        ini_set('memory_limit','256M');
        if ( !$sourceUrl || !$targetFilePath ) return false;

        // 检查目标目录是否存在、创建
        FileUtil::checkDirFileExist( $targetFilePath );

        // 下载
        $CurlUtil = new CurlUtil();
        $fileContent = $CurlUtil->makeRequest( CurlUtil::METHOD_GET, $sourceUrl, [], 30 );
        $savelenght = file_put_contents( $targetFilePath, $fileContent[ 'result' ] );

        $mod && chmod( $targetFilePath, $mod );
        unset( $fileContent );

        return $targetFilePath;
    }

    /**
     * 代码模板 创建 实例
        $createStuFileConfig = [
            'stubPath' => __DIR__.'/stubs/SyncDataLogic.stub',
            'createClassPath' => app_path( 'Logic/Larasearch/'. {createClassName} .'.php' ),
            'targetArray' => [ 'DummyClass', 'DummyTable', ],
            'replaceArray' => [ 'Sync'. {createClassName} .'Logic', $this->inputName, ],
        ];
     */
    public static function createStubFile( array $createStuFileConfig )
    {
        if ( !isset( $createStuFileConfig[ 'stubPath' ] ) || !isset( $createStuFileConfig[ 'createClassPath' ] )
            || !isset( $createStuFileConfig[ 'targetArray' ] ) || !isset( $createStuFileConfig[ 'replaceArray' ] )
        ) {
            throw new \Exception( '代码模板 创建 实例中存在必要参数缺失,请检查', false );
        }

        $FileUtil = new FileUtil();

        // 模板文件源
        $fileContent = file_get_contents( $createStuFileConfig[ 'stubPath' ] );

        // 替换模板 内容
        $fileContent = str_replace(
            $createStuFileConfig[ 'targetArray' ]
            , $createStuFileConfig[ 'replaceArray' ]
            , $fileContent
        );

        // 生成逻辑类
        $FileUtil->checkDirFileExist( $createStuFileConfig[ 'createClassPath' ] );
        return file_put_contents( $createStuFileConfig[ 'createClassPath' ], $fileContent );
    }
}