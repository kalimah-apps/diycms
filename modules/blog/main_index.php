<?php
/*
+===============================================================================+
|      					DIY-CMS V1.1 Copyright � 2011   						|
|   	--------------------------------------------------------------   		|
|                    				BY                    						|
|              				ABDUL KAHHAR AL-HASANY            					|
|   																	   		|
|      					Web: http://www.diy-cms.com      						|
|   	--------------------------------------------------------------   		|
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR	|
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,		|
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE	|
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER		|
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING		|
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS	|
* IN THE SOFTWARE.																|
+===============================================================================+
*/

//----------------------------------------------
// This file will be viewed in the cms index page if the blog module is selected to be viewed in the index page
//----------------------------------------------
$mod = new module('blog');
define('RUN_MODULE', true);
include("modules/".$mod->module."/includes/functions.php");

 $start =(!isset($_GET['start'])) ? '0' : $_GET['start'];
 
// view the posts in this category
 $posts_per_page    = $mod->setting("posts_per_page");
 $comments_per_page = $mod->setting("comments_per_page");
 $sort_by           = $mod->setting("sort_posts_by");
 $getorder_by       = $mod->setting("order_posts_by");
 $head_letters 		= $mod->setting("post_head_letters");
 
 if ($getorder_by == "last_added") {
     $order_by = '.date_added';
 } elseif ($getorder_by == "last_added_comment") {
     $order_by = '_comment.date_added';
 } elseif ($getorder_by == "comments_number") {
     $order_by = '.comments_no';
 } elseif ($getorder_by == "readers") {
     $order_by = '.readers';
 }
 
 $topics_number = $diy_db->dbnumquery("diy_blogs", "draft = '0'");
 
// Type the naviagation bar	
	$result = $diy_db->query("SELECT diy_blogs.*,COUNT(diy_blogs_comments.blog_id) as numrows
                                FROM diy_blogs LEFT JOIN diy_blogs_comments
                                ON diy_blogs.blog_id = diy_blogs_comments.blog_id
                                WHERE diy_blogs.draft = '0'
                                GROUP BY diy_blogs.blog_id
                                ORDER BY diy_blogs$order_by $sort_by
								LIMIT $start,$posts_per_page");
 
 while ($row = $diy_db->dbarray($result)) {
     extract($row);
     $title   = format_data_out($title);
     $name    = format_data_out($username);
     $pagenum = pagination_list($numrows, $comments_per_page, "mod.php?mod=blog&modfile=viewpost&blogid=$blog_id");
     $date    = format_date($date_added);
     $tags	  = get_blog_tags($tags);
	
	
	$post   = replace_censored_words ($post);
	if($head_letters !== '-1')
		 {
         $post  = limit_text_view($post, $head_letters);
		 }	
	$post 	= view_attachment_images($blog_id, 'post', $post);
	$post	= post_output($post, get_group_setting('editor_type'));
	
	//$post 	= preg_replace('@\[.+?\]@is', '', $post);
	$post 	= replace_smile_images($post);
	$post   = highlight_words($post);
	
	
	
     
     if (($head_letters != 0) && ($topics_number > 0)) {
         
         eval("\$index_middle .= \" " . $mod->gettemplate('blog_list_post_head') . "\";");
	$edit_perm = $mod->setting('edit_all_posts',$_COOKIE['cgroup']);
	if($edit_perm)
	{
        $index_middle .= admin_jumpmenu($blog_id,$status);
	}
     } else {
         eval("\$list_row .= \" " . $mod->gettemplate('blog_list_topics_row') . "\";");
     }
     
 }
 

 if (($head_letters == 0) && ($topics_number > 0)) {
     eval("\$index_middle .= \" " . $mod->gettemplate('blog_list_topics') . "\";");
 }

 
 $index_middle .= pagination($topics_number, $posts_per_page, "mod.php?mod=blog&modfile=index");



?>