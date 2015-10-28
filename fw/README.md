# ORC框架使用说明 #
## 说明 ##
框架还在不断完善中，**不认识作者的人请不要使用**，谢谢。  
作者只是因为公司工作需要编写这个框架，只对公司代码负责，不对任何其他使用此框架的任何项目负责。
## 基本组成部分 ##
1. Module。模块包含了接近完整的MVC结构，由Action处理事务，Model处理数据，View（和Template）处理显示。  
2. Template。框架包含两级Template系统，这里指外围大Template。通过配置文件，可以为页面灵活选择布局。框架支持任意多个template。  
3. Block。block配合大template系统，可以实现独立灵活的显示效果。block分为两类，模板类和标准类，分别用于不同的场景。block相关的文件一般存放于module模块目录下。  
4. api。目前实现了内部通讯api，使用http restful协议，标准的gzcompress压缩，可以方便高效地连接别的内部系统。  
5. 队列。目前实现了使用beanstalkd作为队列服务器，pheanstalk作为客户端的队列系统。将来计划使用gearman实现同步队列系统。  
## 框架特点 ##
1. MVC结构。
2. 灵活的Template系统。
3. 简单易用的内部api管理
4. 有限度地支持分布式。
5. 使用composer管理所有第三方库
6. 封装较为完善的数据库操作对象，包括DAO、DBAL和CRUD三种不同类型满足不同需求。

## 项目地址 ##
- 访问[https://gitlab.com/orcworks/frameworks](https://gitlab.com/orcworks/frameworks)获得框架代码
- 访问[https://gitlab.com/orcworks/frameworkdemo](https://gitlab.com/orcworks/frameworkdemo)获得demo项目

## 运行要求 ##
### 环境 ###
开发使用LAMP，PHP版本要求5.3+，不过以后可能会要求5.5+  
作者测试的环境是Ubuntu14.04，apache2.4，PHP5.5，MySQL5.6，绝大部分软件都是用ubuntu 14.04的apt-get安装。
### 运行前准备 ###
安装composer并且在框架根目录执行composer install或者composer update等安装第三方类库。第三方类库将会存入根目录/vendor目录  
在项目根目录建立一个logs目录并设置权限为所有人可读可写  
根据需求修改项目根目录/config里的文件
将框架根目录放在和项目平级的目录中，修改项目public_html/index.php正确引入框架  
如果有必要，注意修改public_html/.htaccess文件。如果使用nginx，自己配一个rewrite文件吧。  
如果有数据库，则需要生成schema文件。使用console执行框架根目录下的tools/updateschema.php，加上-h参数以获得帮助。  
如果还有问题，请直接联系作者。

## 详细说明 ##
### HTTP请求生命周期 ###
1. 请求到达服务器，通过转发访问public_html/index.php文件
2. 通过Application调用Controller调用Route类确定执行的Action名称，并将提交的数据存入Request类
3. Action调用model（可选）并返回相应的Response
4. View通过小template系统得到执行结果
5. 大Template系统将view执行结果包含后发回给客户端

### 命名空间、文件结构及类名 ###
#### 命名空间 ####
##### 框架 #####
1. 框架的代码绝大部分在orc目录下，命名空间和目录对应，ORC开头。例如DataRow，命名空间为ORC\DAO\Table，则文件在orc/dao/table/目录下
2. 部分项目代码在default目录下。此目录为default模块，当项目中用到default模块且项目没有定义的时候，会在这个目录下寻找。

##### 项目 #####
1. Actions/Models/Blocks/Views/Templates等均在各自目录下，没有使用命名空间（namespace），文件名为{名称}.{类型}.php，举例说明：
	1. module名称为module, action名称为do，则action类的名称为Module_DO_Action，文件名则为modules/actions/do.action.php
	2. module名称为module，action名称为sub.do，则action类名为Module_Sub_Do_Action, 文件名为modules/actions/sub/do.action.php。
	3. 其他类型基本文件名、类名同理。
2. 