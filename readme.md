## Laravel With One Record 特点

###1.每个数据model与缓存对应,事务同步保存
###2.既可以使用model原始方法操作数据库,也可以使用新的model进行数据库操作
3.统一Exception处理机制
4.InputData参数过滤
5.缓存使用灵活,可选使用缓存服务器
6.多数据库服务器使用

##目录结构 保持laravel5.0风格
app 程序主要目录
  -DataModel 实现的一套One Record 模型
  -Events 处理多模型之间交互操作的事件
  -Facades 可以静态调用的系统别名
  -Helpers 第三方公共使用的类文件
  -Http 传统mvc结构里的controller还有request信息
  -Exceptions 异常信息处理
  -Model 数据模型
  -Providers 为Facades 提供别名处理
  -Services 第三方服务
public 程序入口和css js img 资源文件目录
storage 文件缓存目录
config 系统配置文件目录
databases 数据库文件目录
resources view展示层
