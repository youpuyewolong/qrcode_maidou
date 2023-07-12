<?php
/**
 * 源码名：caozha-admin
 * Copyright © 邓草 （官网：http://caozha.com）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * caozha-admin (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/cao-zha/caozha-admin   or   Gitee：https://gitee.com/caozha/caozha-admin
 */

namespace app\admin\controller;


use chillerlan\QRCode\Output\QRCodeOutputException;
use chillerlan\QRCode\Output\QRImage;
use chillerlan\QRCode\QROptions;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use app\admin\model\Member as MemberModel;

class Qrcode
{
    protected $middleware = [
        'caozha_auth' 	=> ['except' => '' ],//验证是否管理员
    ];

    public function __construct(){
        cz_auth("qrcode");//检测是否有权限
    }

    public function index()
    {
        $web_config=get_web_config();
        $limit=$web_config["member_limit"];
        if(!is_numeric($limit)){
            $limit=15;//默认显示15条
        }

        View::assign([
            'type' => array(1 => "图片",2 => "视频",3 => "文件",'4'=>'链接'),
            'member_limit'  => $limit,
        ]);
        // 模板输出
        return View::fetch('qrcode/index');
    }

    public function add()
    {

        View::assign([
            'type'  => [['key'=>'1','value'=>'图片'],['key'=>'2','value'=>'视频'],['key'=>'3','value'=>'文件'],['key'=>'4','value'=>'链接']],
        ]);
        // 模板输出
        return View::fetch('qrcode/add');
    }

    public function addSave()
    {
        if(!Request::isAjax()){
            // 如果不是AJAX
            return result_json(0,"error");
        }

        $update_data=Request::param('','','filter_sql');//过滤注入

        $update_data["regtime"]=date("Y-m-d H:i:s",time());

        $member = new \app\admin\model\Qrcode();
        $userid = $member->save($update_data);
        //$userid = Db::name('member')->insertGetId($update_data);

        if($userid>0){
            write_syslog(array("log_content"=>"新增二维码，ID：".$userid));//记录系统日志
            $list=array("code"=>1,"update_num"=>1,"msg"=>"添加成功");
        }else{
            $list=array("code"=>0,"update_num"=>0,"msg"=>"添加失败");
        }
        return json($list);
    }

    public function edit()
    {
        $userid=Request::param("qid",'','filter_sql');

        $member=\app\admin\model\Qrcode::where("qid","=",$userid)->findOrEmpty();

        View::assign([
            'type'  => [['key'=>'1','value'=>'图片'],['key'=>'2','value'=>'视频'],['key'=>'3','value'=>'文件'],['key'=>'4','value'=>'链接']],
            'member'  => $member,
        ]);

        // 模板输出
        return View::fetch('qrcode/edit');
    }

    public function editSave()
    {
        if(!Request::isAjax()){
            // 如果不是AJAX
            return result_json(0,"error");
        }
        $update_data=Request::param('','','filter_sql');//过滤注入
        if(!is_numeric($update_data["qid"])){
            caozha_error("参数错误","",1);
        }

        $member=\app\admin\model\Qrcode::where("qid","=",$update_data["qid"])->findOrEmpty();

        if ($member->isEmpty()) {//数据不存在
            $update_result=false;
        }else{
            $update_result=$member->save($update_data);
        }

        if($update_result){
            write_syslog(array("log_content"=>"修改用户，ID：".$update_data["qid"]));//记录系统日志
            $list=array("code"=>1,"update_num"=>1,"msg"=>"保存成功");
        }else{
            $list=array("code"=>0,"update_num"=>0,"msg"=>"保存失败");
        }
        return json($list);
    }

    public function view()
    {
        $qid=Request::param("qid",'','filter_sql');
        $data = 'http://'.$_SERVER['HTTP_HOST'].'/qrcode.html?qid='.$qid;

        // quick and simple:
        $options = new LogoOptions;

        $options->version          = 7;
        $options->eccLevel         = \chillerlan\QRCode\QRCode::ECC_H;
        $options->imageBase64      = false;
        $options->logoSpaceWidth   = 13;
        $options->logoSpaceHeight  = 13;
        $options->scale            = 5;
        $options->imageTransparent = false;
        $options->imageBase64 = true;

        header('Content-type: image/png');

        $qrOutputInterface = new QRImageWithLogo($options, (new QRCode($options))->getMatrix($data));

        $qrcode_url = $qrOutputInterface->dump(null, './logo.png');

        View::assign([
            'qrcode_url'  => $qrcode_url
        ]);
        return View::fetch('qrcode/view');
    }

    public function get()//获取用户数据
    {
        $page=Request::param("page");
        if(!is_numeric($page)){$page=1;}
        $limit=Request::param("limit");
        if(!is_numeric($limit)){
            $web_config=get_web_config();
            $limit=$web_config["member_limit"];
            if(!is_numeric($limit)){
                $limit=15;//默认显示15条
            }
        }

        $list=Db::name('qrcode');

        $list=$list->withAttr('type', function($value) {
            $type = [1=>'图片',2=>'视频','3'=>'附件','4'=>'链接'];
            return $type[$value];
        })->order('qid', 'desc');

        $action=Request::param('','','filter_sql');//过滤注入
        if(isset($action["keyword"])){
            if($action["keyword"]!=""){
                $list=$list->where("title","like","%".$action["keyword"]."%");
            }
        }
        if(isset($action["type"])){
            if($action["type"]!=""){
                $list=$list->where("type","=",$action["type"]);
            }
        }
        $list=$list->paginate([
            'list_rows'=> $limit,//每页数量
            'page' => $page,//当前页
        ]);
        //print_r(Db::getLastSql());
        return json($list);
    }

    public function delete()//删除数据
    {
        //执行删除
        $userid=Request::param("qid",'','filter_sql');
        $del_num=0;
        if($userid){
            $del_num=\app\admin\model\Qrcode::where("qid","in",$userid)->delete();
        }
        if($del_num>0){
            write_syslog(array("log_content"=>"删除二维码(ID)：".$userid));//记录系统日志
            $list=array("code"=>1,"del_num"=>$del_num);
        }else{
            $list=array("code"=>0,"del_num"=>0);
        }

        return json($list);
    }

}

class LogoOptions extends QROptions{
    // size in QR modules, multiply with QROptions::$scale for pixel size
    public int $logoSpaceWidth;
    public int $logoSpaceHeight;
}

class QRImageWithLogo extends QRImage{

    /**
     * @param string|null $file
     * @param string|null $logo
     *
     * @return string
     * @throws \chillerlan\QRCode\Output\QRCodeOutputException
     */
    public function dump(string $file = null, string $logo = null):string{
        // set returnResource to true to skip further processing for now
        $this->options->returnResource = true;
        // of course you could accept other formats too (such as resource or Imagick)
        // i'm not checking for the file type either for simplicity reasons (assuming PNG)
        if(!is_file($logo) || !is_readable($logo)){
            throw new QRCodeOutputException('invalid logo');
        }

        $this->matrix->setLogoSpace(
            $this->options->logoSpaceWidth,
            $this->options->logoSpaceHeight
        // not utilizing the position here
        );

        // there's no need to save the result of dump() into $this->image here
        parent::dump($file);

        $im = imagecreatefrompng($logo);

        // get logo image size
        $w = imagesx($im);
        $h = imagesy($im);

        // set new logo size, leave a border of 1 module (no proportional resize/centering)
        $lw = ($this->options->logoSpaceWidth - 2) * $this->options->scale;
        $lh = ($this->options->logoSpaceHeight - 2) * $this->options->scale;

        // get the qrcode size
        $ql = $this->matrix->size() * $this->options->scale;

        // scale the logo and copy it over. done!
        imagecopyresampled($this->image, $im, ($ql - $lw) / 2, ($ql - $lh) / 2, 0, 0, $lw, $lh, $w, $h);

        $imageData = $this->dumpImage();

        if($file !== null){
            $this->saveToFile($imageData, $file);
        }

        if($this->options->imageBase64){
            $imageData = 'data:image/'.$this->options->outputType.';base64,'.base64_encode($imageData);
        }

        return $imageData;
    }

}
