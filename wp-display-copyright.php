<?php
/*
 * Plugin Name:WP Display Copyright
 * Plugin URI:http://www.waitig.com
 * Description:在您的站点文章页面和feed页面显示您的版权标记，其内容可以自行修改
 * Author:waitig
 * Version:1.0
 * Author URI:www.waitig.com
 */

/* 注册激活插件时要调用的函数 */ 
register_activation_hook( __FILE__, 'wp_display_copyright_install');   

/* 注册停用插件时要调用的函数 */ 
register_deactivation_hook( __FILE__, 'wp_display_copyright_remove' );  

/*定义wp_display_copyright_text变量*/
function wp_display_copyright_install() {  
		/* 在数据库的 wp_options 表中添加一条记录，第二个参数为默认值 */ 
		add_option("display_copyright_text",'转载请注明：<a href="{{blog_link}}">{{blog_name}}</a>++><a href="{{link}}">{{title}}</a>','','yes');  
}
function wp_display_copyright_remove() {  
		/* 删除 wp_options 表中的对应记录 */ 
		delete_option('display_copyright_text');  
}
if( is_admin() ) {
		/*  利用 admin_menu 钩子，添加菜单 */
		add_action('admin_menu', 'wp_display_copyright_menu');
}

function wp_display_copyright_menu() {
		/* add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);  */
		/* 页名称，菜单名称，访问级别，菜单别名，点击该菜单时的回调函数（用以显示设置页面） */
		add_options_page('设定文章版权信息', 'Copyright Menu', 'administrator','display_copyright', 'display_copyright_html_page');
}
function display_copyright_html_page() {
?>
	<div>  
		<h2>Set Copyright</h2>  
		<form method="post" action="options.php">  
			<?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>  
			<?php wp_nonce_field('update-options'); ?>  
			<p>请在下方输入版权信息，支持以下字符：</p>
<p>{{title}}--代表文章标题</p>
<p>{{link}}--代表文章链接</p>
<p>{{blog_name}}--代表博客名称</p>
<p>{{blog_link}}--代表博客主页链接</p>
<p>完美支持HTML标签</P>
			<p>
				<textarea  
					name="display_copyright_text" 
					id="display_copyright_text" 
					cols="60" 
					rows="20"><?php echo get_option('display_copyright_text'); ?></textarea>  
			</p>  
			<p>  
				<input type="hidden" name="action" value="update" />  
				<input type="hidden" name="page_options" value="display_copyright_text" />  
				<input type="submit" value="Save" class="button-primary" /> 
			</p>  
		</form>
<p>如有问题，请反馈至：<a href="http://www.waitig.com">等英博客</a></p>  
	</div>  
<?php  
}  
add_filter( 'the_content',  'display_copyright' );  

/* 这个函数在日志正文结尾处添加一段版权信息，在 文章和feed 页面都添加 */ 
function display_copyright( $content ) {
	   if (is_single() || is_feed()) {
		$copyright = str_replace(array('{{title}}','{{link}}','{{blog_name}}','{{blog_link}}'), array(get_the_title(), get_permalink(),get_bloginfo('name'),get_bloginfo('url')), stripslashes(get_option('display_copyright_text')));
		$content.= '<hr /><div align="left" style="margin-bottom: 10px;padding:5px 20px;border-radius: 5px;background-color: #fcf8e3;border: 1px solid #4094EF;color: #8a6d3b"><i class="fa fa-bullhorn" style="text-indent:-20px"></i>' . $copyright . '</div>';
    }
    return $content;		
} 
?>
