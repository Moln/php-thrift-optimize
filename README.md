php-thrift-optimize
===================


原Thrift 生成的PHP太大, 代码太多重复, 此功能对 Thrift 生成的PHP 进行优化

##使用说明

-步骤1: composert 安装下载 thrift 依赖库-
`composer install`
会进行下载 thrift 库

-步骤2: 进入项目目录,用thrift生成php类-
`thrift -gen php THbaseService.thrift`

-步骤3: 执行优化命令-
`php bin/ThriftOptimize.php` 提示输入thrift 类
输入 `Hbase.THbaseService` , 会生成 source 目录, 里面就是生成优化后的代码
