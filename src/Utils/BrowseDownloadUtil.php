<?php
/**
 * Function:
 * Description:
 * Abo 2019/4/4 11:39
 * Email: abo2013@foxmail.com
 */

namespace Abo\Generalutil\Utils;


class BrowseDownloadUtil
{

    /** 输出素材文件 */
    public function outputMaterialPass( string $url )
    {
        if ( !$url ) { return '暂无信息'; }
        try {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename( $url ).'"');
            header('Expires: 0');
            header('Cache-Control：must-revalidate，post-check = 0，pre-check = 0');
            header('Pragma: public');

            ob_clean();
            flush();

            ob_start();
            $filename4Local = $this->transformUrl2LocalFile( $url );
            if ( file_exists( $filename4Local ) ) {
                $this->readLocateFile2Browse( $filename4Local );
            }elseif ( strstr( 'http', $url ) ) {
                $this->readCurlFile2BrowseV2( $url );
            }
            // $str = ob_get_contents();
            ob_flush();
            flush();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    /** curl请求文件 */
    protected function readCurlFile2BrowseV2( string $filename )
    {
        if ( strstr( 'http', $filename ) ) { return ''; }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $filename);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);
        $fileContent = curl_exec($ch);
        curl_close($ch);

        echo $fileContent;
        return true;
    }

    /** 本地读取输出文件 */
    protected function readLocateFile2Browse( string $localFilePath )
    {
        if( !is_file( $localFilePath )
            || !is_readable( $localFilePath )
        ) { return false; }

        $sum_buffer = 0; // 总的缓冲的字节数
        $read_buffer = 4096; // 针对大文件，规定每次读取文件的字节数为4096字节，直接输出数据
        $filesize = filesize( $localFilePath );
        $handle = fopen( $localFilePath, 'rb' );

        set_time_limit( 0 );
        header( 'Accept-Ranges:bytes' ); //告诉浏览器返回的文件大小类型是字节
        header( 'Accept-Length:'.$filesize ); //告诉浏览器返回的文件大小

        while( !feof( $handle ) && $sum_buffer < $filesize ) {  // 只要没到文件尾，就一直读取
            echo fread( $handle, $read_buffer );
            ob_flush(); // 把数据从PHP的缓冲中释放出来
            flush(); // 把被释放出来的数据发送到浏览器
            $sum_buffer += $read_buffer;
        }

        //关闭句柄
        fclose( $handle );
        return true;
    }

    /** Url转换为本地文件路径 */
    protected function transformUrl2LocalFile( string $url )
    {
        if ( !$url ) { return false; }

        $searchParam = [
            'http://statictest.superdalan.com/',
            'https://statictest.superdalan.com/',
            'http://static.superdalan.com/',
            'https://static.superdalan.com/',
        ];
        $filePath = str_replace( $searchParam, config( 'material_dir', '/shuiyin/' ), $url );
        return $filePath;
    }
}