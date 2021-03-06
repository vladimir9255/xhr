<?php

class form {

    /**
     * 编辑器
     * @param int $textareaid
     * @param int $toolbar
     * @param string $module 模块名称
     * @param int $catid 栏目id
     * @param int $color 编辑器颜色
     * @param boole $allowupload  是否允许上传
     * @param boole $allowbrowser 是否允许浏览文件
     * @param string $alowuploadexts 允许上传类型
     * @param string $height 编辑器高度
     * @param string $disabled_page 是否禁用分页和子标题
     */
    public static function editor($textareaid = 'content', $toolbar = 'basic', $module = '', $color = '', $allowupload = 0, $allowbrowser = 1, $alowuploadexts = '', $height = 200, $disabled_page = 0, $allowuploadnum = '10') {
        $str = '';
        if (!defined('EDITOR_INIT')) {
            $str = '<script type="text/javascript" src="' . CDN_PATH . 'js/ckeditor/ckeditor.js"></script>';
            define('EDITOR_INIT', 1);
        }
        if ($toolbar == 'basic') {
            $toolbar = defined('IN_ADMIN') ? "['Source']," : '';
            $toolbar .= "['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink' ],['Maximize'],\r\n";
        } elseif ($toolbar == 'full') {
            if (defined('IN_ADMIN')) {
                $toolbar = "['Source',";
            } else {
                $toolbar = '[';
            }
            $toolbar .= "'-','Templates'],
		    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
		    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],['ShowBlocks'],['Image','Capture','Flash'],['Maximize'],
		    '/',
		    ['Bold','Italic','Underline','Strike','-'],
		    ['Subscript','Superscript','-'],
		    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
		    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		    ['Link','Unlink','Anchor'],
		    ['Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
		    '/',
		    ['Styles','Format','Font','FontSize'],
		    ['TextColor','BGColor'],
		    ['attachment'],\r\n";
        } elseif ($toolbar == 'desc') {
            $toolbar = "['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'Image', '-','Source'],['Maximize'],\r\n";
        } else {
            $toolbar = '';
        }
        $str .= "<script type=\"text/javascript\">\r\n";
        $str .= "CKEDITOR.replace( '$textareaid',{";
        $str .= "height:{$height},";

        $show_page = ($module == 'content' && !$disabled_page) ? 'true' : 'false';
        $str .="pages:$show_page,subtitle:$show_page,textareaid:'" . $textareaid . "',module:'" . $module . "',catid:'" . $catid . "',\r\n";
        if ($allowupload) {
            $isadmin = defined('IN_ADMIN') ? 1 : 0;
            $authkey = upload_key("$allowuploadnum,$alowuploadexts,$allowbrowser,,,$isadmin");
            $str .="flashupload:true,alowuploadexts:'" . $alowuploadexts . "',allowbrowser:'" . $allowbrowser . "',allowuploadnum:'" . $allowuploadnum . "',authkey:'" . $authkey . "',isadmin:" . $isadmin . ",\r\n";
        }
        if ($allowupload)
            $str .= "filebrowserUploadUrl : '" . APP_PATH . "index.php?m=attachment&c=attachments&a=upload&module=" . $module . "&isadmin=" . $isadmin . "&dosubmit=1',\r\n";
        if ($color) {
            $str .= "extraPlugins : 'uicolor',uiColor: '$color',";
        }
        $str .= "toolbar :\r\n";
        $str .= "[\r\n";
        $str .= $toolbar;
        $str .= "]\r\n";
        //$str .= "fullPage : true";
        $str .= "});\r\n";
        $str .= '</script>';
        $ext_str = "<div class='editor_bottom'>";
        if (!defined('IMAGES_INIT')) {
            $ext_str .= '<script type="text/javascript" src="' . CDN_PATH . 'js/swfupload/swf2ckeditor.js"></script>';
            define('IMAGES_INIT', 1);
        }
        $ext_str .= "<div id='page_title_div'>
		<table cellpadding='0' cellspacing='1' border='0'><tr><td class='title'>" . L('subtitle') . "<span id='msg_page_title_value'></span></td><td>
		<a class='close' href='javascript:;' onclick='javascript:$(\"#page_title_div\").hide();'><span>×</span></a></td>
		<tr><td colspan='2'><input name='page_title_value' id='page_title_value' class='input-text' value='' size='30'>&nbsp;<input type='button' class='button' value='" . L('submit') . "' onclick=insert_page_title(\"$textareaid\",1)></td></tr>
		</table></div>";
        $ext_str .= "</div>";
        if (is_ie())
            $ext_str .= "<div style='display:none'><OBJECT id='PC_Capture' classid='clsid:021E8C6F-52D4-42F2-9B36-BCFBAD3A0DE4'><PARAM NAME='_Version' VALUE='0'><PARAM NAME='_ExtentX' VALUE='0'><PARAM NAME='_ExtentY' VALUE='0'><PARAM NAME='_StockProps' VALUE='0'></OBJECT></div>";
        $str .= $ext_str;
        return $str;
    }

    /**
     *
     * @param string $name 表单名称
     * @param int $id 表单id
     * @param string $value 表单默认值
     * @param int $size 表单大小
     * @param string $class 表单风格
     * @param string $ext 表单扩展属性 如果 js事件等
     * @param string $alowexts 允许图片格式
     * @param array $thumb_setting
     * @param int $watermark_setting  0或1
     */
 public static function images($name, $id = '', $model = '', $value = '', $size = 50, $class = '', $ext = '', $alowexts = '', $thumb_setting = array(), $watermark_setting = 0) {
        if (!$id)
            $id = $name;
        if (!$size)
            $size = 50;
        if (!empty($thumb_setting) && count($thumb_setting))
            $thumb_ext = $thumb_setting[0] . ',' . $thumb_setting[1];
        else
            $thumb_ext = ',';
        if (!$alowexts)
            $alowexts = 'jpg|jpeg|gif|bmp|png';
        $isadmin = defined('IN_ADMIN') ? 1 : 0;
        if (!defined('IMAGES_INIT')) {
            if ($isadmin) {
                $str = '<script type="text/javascript" src="' . CDN_PATH . 'js/swfupload/swf2ckeditor.js"></script>';
            } else {
                $str = '<script type="text/javascript" src="' . CDN_PATH . 'js/swfupload/swfckeditor.js"></script>';
            }
            define('IMAGES_INIT', 1);
        }
        $key_str = "1,$alowexts,1,$thumb_ext,$watermark_setting,$isadmin";
        $authkey = upload_key($key_str);
        return $str . "<input type=\"text\" name=\"$name\" id=\"$id\" value=\"$value\" size=\"$size\" class=\"$class\" $ext/> 
        <input type=\"button\" class=\"button\" onclick=\"javascript:flashupload('{$id}_images', '上传','{$id}',submit_images,
        '$model','$key_str',$isadmin,'$authkey')\"/ value=\"上传\">";
    }

    /**
     * 日期时间控件
     *
     * @param $name 控件name，id
     * @param $value 选中值
     * @param $isdatetime 是否显示时间
     * @param $loadjs 是否重复加载js，防止页面程序加载不规则导致的控件无法显示
     * @param $showweek 是否显示周，使用，true | false
     * @param $isshowday 是否显示日期（到年月），使用，1 | 0 （20140807弄交易明细补的）
     */
    public static function date($name, $value = '', $isdatetime = 0, $loadjs = 0, $showweek = 'true',$isshowday=0) {
        if ($value == '0000-00-00 00:00:00')
            $value = '';
        $id = preg_match("/\[(.*)\]/", $name, $m) ? $m[1] : $name;
        if ($isdatetime) {
            $size = 21;
            $format = '%Y-%m-%d %H:%M:%S';
            $showsTime = true;
        } else {
            $size = 10;
            $format = '%Y-%m-%d';
            $showsTime = 'false';
        }
        if($isshowday){
            $format = '%Y-%m';
        }
        $str = '';
        if ($loadjs || !defined('CALENDAR_INIT')) {
            define('CALENDAR_INIT', 1);
            $str .= '<link rel="stylesheet" type="text/css" href="' . CDN_PATH . 'js/calendar/jscal2.css"/>
			<link rel="stylesheet" type="text/css" href="' . CDN_PATH . 'js/calendar/border-radius.css"/>
			<link rel="stylesheet" type="text/css" href="' . CDN_PATH . 'js/calendar/win2k.css"/>
			<script type="text/javascript" src="' . CDN_PATH . 'js/calendar/calendar.js"></script>
			<script type="text/javascript" src="' . CDN_PATH . 'js/calendar/lang/en.js"></script>';
        }
        $str .= '<input type="text" name="' . $name . '" id="' . $id . '" value="' . $value . '" size="' . $size . '" class="date form-control" readonly>&nbsp;';
        $str .= '<script type="text/javascript">
			Calendar.setup({
			weekNumbers: ' . $showweek . ',
		    inputField : "' . $id . '",
		    trigger    : "' . $id . '",
		    dateFormat: "' . $format . '",
		    showTime: ' . $showsTime . ',
		    onSelect   : function() {this.hide();}
			});
        </script>';
        return $str;
    }

    /**
     * 下拉选择框
     */
    public static function select($array = array(), $id = 0, $str = '', $default_option = '') {
        $string = '<select ' . $str . '>';
        $default_selected = (empty($id) && $default_option) ? 'selected' : '';
        if ($default_option)
            $string .= "<option value='' $default_selected>$default_option</option>";
        if (!is_array($array) || count($array) == 0)
            return false;
        $ids = array();
        if ($id)
            $ids = explode(',', $id);
        foreach ($array as $key => $value) {
            $selected = in_array($key, $ids) ? 'selected' : '';
            $string .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $string .= '</select>';
        return $string;
    }

    /**
     * 复选框
     *
     * @param $array 选项 二维数组
     * @param $id 默认选中值，多个用 '逗号'分割
     * @param $str 属性
     * @param $defaultvalue 是否增加默认值 默认值为 -99
     * @param $width 宽度
     */
    public static function checkbox($array = array(), $id = '', $str = '', $defaultvalue = '', $width = 0, $field = '') {
        $string = '';
        $id = trim($id);
        if ($id != '')
            $id = strpos($id, ',') ? explode(',', $id) : array($id);
        if ($defaultvalue)
            $string .= '<input type="hidden" ' . $str . ' value="-99">';
        $i = 1;
        foreach ($array as $key => $value) {
            $key = trim($key);
            $checked = ($id && in_array($key, $id)) ? 'checked' : '';
            if ($width)
                $string .= '<label class="ib" style="width:' . $width . 'px">';
            $string .= '<input type="checkbox" ' . $str . ' id="' . $field . '_' . $i . '" ' . $checked . ' value="' . htmlspecialchars($key) . '"> ' . htmlspecialchars($value);
            if ($width)
                $string .= '</label>';
            $i++;
        }
        return $string;
    }

    /**
     * 单选框
     *
     * @param $array 选项 二维数组
     * @param $id 默认选中值
     * @param $str 属性
     */
    public static function radio($array = array(), $id = 0, $str = '', $width = 0, $field = '') {
        $string = '';
        foreach ($array as $key => $value) {
            $checked = trim($id) == trim($key) ? 'checked' : '';
            if ($width)
                $string .= '<label class="ib" style="width:' . $width . 'px">';
            $string .= '<input type="radio" ' . $str . ' id="' . $field . '_' . htmlspecialchars($key) . '" ' . $checked . ' value="' . $key . '"> ' . $value;
            if ($width)
                $string .= '</label>';
        }
        return $string;
    }

    /**
     * 模板选择
     *
     * @param $style  风格
     * @param $module 模块
     * @param $id 默认选中值
     * @param $str 属性
     * @param $pre 模板前缀
     */
    public static function select_template($style, $module, $id = '', $str = '', $pre = '') {
        $tpl_root = pc_base::load_config('system', 'tpl_root');
        $templatedir = PC_PATH . $tpl_root . DIRECTORY_SEPARATOR . $style . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR;
        $confing_path = PC_PATH . $tpl_root . DIRECTORY_SEPARATOR . $style . DIRECTORY_SEPARATOR . 'config.php';
        $localdir = str_replace(array('/', '\\'), '', $tpl_root) . '|' . $style . '|' . $module;
        $templates = glob($templatedir . $pre . '*.html');
        if (empty($templates)) {
            $style = 'default';
            $templatedir = PC_PATH . $tpl_root . DIRECTORY_SEPARATOR . $style . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR;
            $confing_path = PC_PATH . $tpl_root . DIRECTORY_SEPARATOR . $style . DIRECTORY_SEPARATOR . 'config.php';
            $localdir = str_replace(array('/', '\\'), '', $tpl_root) . '|' . $style . '|' . $module;
            $templates = glob($templatedir . $pre . '*.html');
        }
        if (empty($templates))
            return false;
        $files = @array_map('basename', $templates);
        $names = array();
        if (file_exists($confing_path)) {
            $names = include $confing_path;
        }
        $templates = array();
        if (is_array($files)) {
            foreach ($files as $file) {
                $key = substr($file, 0, -5);
                $templates[$key] = isset($names['file_explan'][$localdir][$file]) && !empty($names['file_explan'][$localdir][$file]) ? $names['file_explan'][$localdir][$file] . '(' . $file . ')' : $file;
            }
        }
        ksort($templates);
        return self::select($templates, $id, $str, L('please_select'));
    }

    /**
     * 验证码
     * @param string $id            生成的验证码ID
     * @param integer $code_len     生成多少位验证码
     * @param integer $font_size    验证码字体大小
     * @param integer $width        验证图片的宽
     * @param integer $height       验证码图片的高
     * @param string $font          使用什么字体，设置字体的URL
     * @param string $font_color    字体使用什么颜色
     * @param string $background    背景使用什么颜色
     */
    public static function checkcode($id = 'checkcode', $code_len = 4, $font_size = 20, $width = 130, $height = 50, $font = '', $font_color = '', $background = '') {
        return "<img id='$id' onclick='this.src=this.src+\"&\"+Math.random()' src='" . APP_PATH . "api.php?op=checkcode&code_len=$code_len&font_size=$font_size&width=$width&height=$height&font_color=" . urlencode($font_color) . "&background=" . urlencode($background) . "'>";
    }

}

?>