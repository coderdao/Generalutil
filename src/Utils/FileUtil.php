<?php
/**
 * Function:
 * Description:
 * Abo 2019/2/15 11:02
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\Utils;

use Abo\Fasterapi\Exceptions\ApiException;
use Abo\Fasterapi\Exceptions\InnerException;

class FileUtil
{
    /** 检查文件目录是否存在 目录无则创建 文件有则删除 */
    public function checkDirFileExist( string $filePath )
    {
        $dirPath = dirname( $filePath );
        if ( !$dirPath ) { throw new ApiException( 500, '系统异常：暂无目录信息' ); }

        //目录不存在则创建目录
        if ( !is_dir( $dirPath ) && !mkdir( $dirPath, 755,true ) ) {
            throw new ApiException( 500, '系统异常：目录创建失败' );//mkdir没加true是上一级目录不存在就会创建失败
        }

        //生成文件存在 则删除，重新创建
        if (file_exists( $filePath )) {
            unlink( $filePath );
        }

        return true;
    }

    /**
     * 文件重命名
     * @param string $oldFilePath
     * @param string $newFilePath
     * @return bool
     * @throws InnerException
     */
    public function rename( string $oldFilePath, string $newFilePath )
    {
        if ( !$newFilePath )
            { throw new InnerException( '500', '新文件名不能为空:'.$newFilePath ); }
        if ( !$oldFilePath || !file_exists( $oldFilePath ) )
            { throw new InnerException( '500', '重命名文件不存在:'.$oldFilePath ); }
        if ( file_exists( $newFilePath ) ) { return '新文件已存在'; }

        return rename( $oldFilePath, $newFilePath );
    }
}