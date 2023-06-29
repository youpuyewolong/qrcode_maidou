<?php
/**
 * 源码名：caozha-admin
 * Copyright © 邓草 （官网：http://caozha.com）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * caozha-admin (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/cao-zha/caozha-admin   or   Gitee：https://gitee.com/caozha/caozha-admin
 */

// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用地址
    'app_host'         => env('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 是否启用事件
    'with_event'       => true,
    // 默认应用
    'default_app'      => 'admin',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => true,

    //会员，实名状态
    'caozha_member_isrn' => array(0 => "否",1 => "是",2 => "待审"),

    //文章，状态
    'caozha_article_status' => array(0 => "无效",1 => "在审",2 => "退稿",9 => "通过"),

    //分类，栏目类型ID
    'caozha_category_types' => array(0 => "内部栏目",1 => "单网页",2 => "外部链接"),

    //分类，模型ID，0=系统，1=文章
    'caozha_category_modelid' => array(0 => "系统内置",1 => "文章模型",2 => "下载模型",3 => "图片模型",4 => "视频模型"),

    //后台权限数组，开发过程中，必须把所有权限都列出来并与程序内部设定一致，以便验证。标识符必须保持唯一性，不能相同
    'caozha_role_auths'  => array(
        //格式为：'标识符' => array('name'=>'权限名','remarks'=>'权限说明'),
        'config'  =>  array('name'=>'网站配置','remarks'=>'管理网站名称、备案号等一些配置'),
        'roles'  =>  array('name'=>'权限组管理','remarks'=>'可以增删改权限组（拥有此权限相当于超级管理员）'),
        'admin'  =>  array('name'=>'管理员管理','remarks'=>'可以增删改管理员（拥有此权限相当于超级管理员）'),
        'log_view'  =>  array('name'=>'查看系统日志','remarks'=>'可以查看系统日志'),
        'log_del'  =>  array('name'=>'删除系统日志','remarks'=>'可以删除系统日志'),
        'mine'  =>  array('name'=>'修改自己资料','remarks'=>'可以查看修改自己的资料和密码'),
        'category'  =>  array('name'=>'分类管理','remarks'=>'可以增删改分类'),
        'article'  =>  array('name'=>'文章管理','remarks'=>'可以增删改文章'),
        'member'  =>  array('name'=>'用户管理','remarks'=>'可以增删改用户'),
        'mbr_group'  =>  array('name'=>'用户组管理','remarks'=>'可以增删改用户组'),
        'comment'  =>  array('name'=>'评论管理','remarks'=>'可以管理用户发布的评论'),
        'qrcode'  =>  array('name'=>'二维码管理','remarks'=>'可以管理二维码'),
    ),

    //前台用户权限，开发过程中，必须把所有权限都列出来并与程序内部设定一致，以便验证。标识符必须保持唯一性，不能相同
    'caozha_member_role_auths'  => array(
        //格式为：'标识符' => array('name'=>'权限名','remarks'=>'权限说明'),
        'mine'  =>  array('name'=>'修改自己资料和密码','remarks'=>'可以修改自己资料和密码'),
        'cmt'  =>  array('name'=>'评论文章','remarks'=>'可以对文章发布评论'),
    ),

    //后台初始化菜单,json数据
    'caozha_init_config'=>'
 {
  "homeInfo": {
    "title": "首页",
    "href": "'.url("admin/qrcode/index").'"
  },
  "logoInfo": {
    "title": "后台管理系统",
    "image": "'.get_cz_path().'static/admin/caozha/logo/logo.png",
    "href": ""
  },
  "menuInfo": [
    {
      "title": "常规管理",
      "icon": "fa fa-address-book",
      "href": "",
      "target": "_self",
      "child": [      

        {
          "title": "二维码管理",
          "href": "",
          "icon": "fa fa-user",
          "target": "_self",
          "child": [
            {
              "title": "二维码列表",
              "href": "'.url("admin/qrcode/index").'",
              "icon": "fa fa-user-circle-o",
              "target": "_self"
            }
          ]
        }
      ]
    }
  ]
}
',
];
