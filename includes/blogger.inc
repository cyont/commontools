<?php 
function getBlogFeed($feed){
	$file = "$feed";
	// Load specified XML file or report failure
	$xml = simplexml_load_file($file) or die ("Unable to load XML file!");
	// Load blog entries
	$xml =  $xml ->entry;
	// Run loop for the number of available entries
	$entries = array();
	
	foreach( $xml as $row )
	{
		
	   // Display blog entry content
	   $dt=new DateTime($row->updated);
	
	   $startId = strpos($row->id, "blog-") + 5;
	   $blogIdS = (substr($row->id, $startId));
	   $startPost = strpos($blogIdS, "post-") + 5;
	   $blogId = (substr($blogIdS, 0, $startPost - 6));
	   $postId = (substr($blogIdS, $startPost));
	/*
	   $entries .= '<span class="date" id="'.$i.'" style="font-size:18px; font-weight:bold; color:#444;">'.$dt->format("l, F j, Y").'</span>';
	   $entries .= "<hr />";
	   $entries .= '<h2 style="font-weight:normal;">'.$row->title . "</h2>";
	   $entries .= $row->content. "<p>";
	   $entries .= "Posted by " . $row->author[0]->name . " at ". $dt->format('g:i a') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$row->link[1][title][0]."&nbsp;&nbsp;&nbsp;";
	
	   $entries .= '<span class="post-share-buttons">
	<a class="share-button sb-email" href="http://www.blogger.com/share-post.g?blogID='.$blogId.'&amp;postID='.$postId.'&amp;target=email" target="_blank" title="Email This">
	<span class="share-button-link-text">Email This</span></a>
	<a class="share-button sb-blog" href="http://www.blogger.com/share-post.g?blogID='.$blogId.'&amp;postID='.$postId.'&amp;target=blog" onclick="window.open(this.href, &quot;_blank&quot;, &quot;height=270,width=475&quot;); return false;" target="_blank" title="BlogThis!">
	<span class="share-button-link-text">BlogThis!</span></a>
	<a class="share-button sb-twitter" href="http://www.blogger.com/share-post.g?blogID='.$blogId.'&amp;postID='.$postId.'&amp;target=twitter" target="_blank" title="Share to Twitter">
	<span class="share-button-link-text">Share to Twitter</span></a>
	<a class="share-button sb-facebook" href="http://www.blogger.com/share-post.g?blogID='.$blogId.'&amp;postID='.$postId.'&amp;target=facebook" onclick="window.open(this.href, &quot;_blank&quot;, &quot;height=430,width=640&quot;); return false;" target="_blank" title="Share to Facebook">
	<span class="share-button-link-text">Share to Facebook</span></a>
	<a class="share-button sb-buzz" href="http://www.blogger.com/share-post.g?blogID='.$blogId.'&amp;postID='.$postId.'&amp;target=buzz" onclick="window.open(this.href, &quot;_blank&quot;, &quot;height=415,width=690&quot;); return false;" target="_blank" title="Share to Google Buzz">
	<span class="share-button-link-text">Share to Google Buzz</span></a>
	</span><br><br>';
	
	   $entries .= "Labels: ". $row->category['term']."<div id='divider'>&nbsp;</div>";
	   $i++;
	   */
	   $entries[] = array(
	   		'startId'=>$startId,
	   		'blogIds'=>$blogIdS,
	   		'startPost'=>$startPost,
	   		'blogId'=>$blogId,
	   		'postId'=>$postId,
	   		'date'=>$dt,
	   		'title'=>$row->title.'',
	   		'content'=>$row->content,
	   		'author'=>$row->author[0]->name,
	   		'other'=>$row->link[1]['title'][0]	   		
	   );
	}

	return $entries;
}

function embed_blog($feed) {
// Set name of XML file
$file = "$feed";
// Load specified XML file or report failure
$xml = simplexml_load_file($file) or die ("Unable to load XML file!");
// Load blog entries
$xml =  $xml ->entry;
// Run loop for the number of available entries
$entries = "";
$i = 0;
foreach( $xml as $row )
{ 
   // Display blog entry content
   $dt=new DateTime($row->updated);

   $startId = strpos($row->id, "blog-") + 5;
   $blogIdS = (substr($row->id, $startId));
   $startPost = strpos($blogIdS, "post-") + 5;
   $blogId = (substr($blogIdS, 0, $startPost - 6));
   $postId = (substr($blogIdS, $startPost));

   $entries .= '<span class="date" id="'.$i.'" style="font-size:18px; font-weight:bold; color:#444;">'.$dt->format("l, F j, Y").'</span>';
   $entries .= "<hr />";
   $entries .= '<h2 style="font-weight:normal;">'.$row->title . "</h2>";
   $entries .= $row->content. "<p>";
   $entries .= "Posted by " . $row->author[0]->name . " at ". $dt->format('g:i a') . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$row->link[1][title][0]."&nbsp;&nbsp;&nbsp;";

   $entries .= '<span class="post-share-buttons">
<a class="share-button sb-email" href="http://www.blogger.com/share-post.g?blogID='.$blogId.'&amp;postID='.$postId.'&amp;target=email" target="_blank" title="Email This">
<span class="share-button-link-text">Email This</span></a>
<a class="share-button sb-blog" href="http://www.blogger.com/share-post.g?blogID='.$blogId.'&amp;postID='.$postId.'&amp;target=blog" onclick="window.open(this.href, &quot;_blank&quot;, &quot;height=270,width=475&quot;); return false;" target="_blank" title="BlogThis!">
<span class="share-button-link-text">BlogThis!</span></a>
<a class="share-button sb-twitter" href="http://www.blogger.com/share-post.g?blogID='.$blogId.'&amp;postID='.$postId.'&amp;target=twitter" target="_blank" title="Share to Twitter">
<span class="share-button-link-text">Share to Twitter</span></a>
<a class="share-button sb-facebook" href="http://www.blogger.com/share-post.g?blogID='.$blogId.'&amp;postID='.$postId.'&amp;target=facebook" onclick="window.open(this.href, &quot;_blank&quot;, &quot;height=430,width=640&quot;); return false;" target="_blank" title="Share to Facebook">
<span class="share-button-link-text">Share to Facebook</span></a>
<a class="share-button sb-buzz" href="http://www.blogger.com/share-post.g?blogID='.$blogId.'&amp;postID='.$postId.'&amp;target=buzz" onclick="window.open(this.href, &quot;_blank&quot;, &quot;height=415,width=690&quot;); return false;" target="_blank" title="Share to Google Buzz">
<span class="share-button-link-text">Share to Google Buzz</span></a>
</span><br><br>';

   $entries .= "Labels: ". $row->category['term']."<div id='divider'>&nbsp;</div>";
   $i++;
}

echo $entries."<br><br>";
echo ("<span id=".$i."></span><script>var numOfDivs = ".($i-1)."; var height=0; for(x=1;  x <= numOfDivs+1 ; x++){  if((document.getElementById(x).offsetTop - document.getElementById(x-1).offsetTop) > height){ height = (document.getElementById(x).offsetTop - document.getElementById(x-1).offsetTop);}} document.getElementById('frame').style.height = height+'px';</script>");

}


function embed_styled_blog($feed){
?>
<script src="/commontools/jslib/jquery-1.3.2.min.js"></script>
<script src="/commontools/jslib/jquery.scrollTo.js"></script>
<link rel="stylesheet" href="/commontools/phplib/blogger/blogger.css" type="text/css" media="screen" />
	
	<div id="frame" style="height: 600px; overflow:hidden;"><?php embed_blog($feed); ?></div>
	<div align="right"><a href='javascript:down();'><img src="/involvement/template/images/down_arrow.gif" /></a> <a href='javascript:up();'><img src="/involvement/template/images/up_arrow.gif" /></a></div>
	
	<script>
	var i = 0;
	
	function up()
	{
		if(i > 0)
		{
			i--;
			$('#frame').scrollTo('#'+i, 800)
		}
	}
	
	function down()
	{	
		if(i < numOfDivs)
		{
			i++;
			$('#frame').scrollTo('#'+i, 800)
		}
	}
	</script>
<?php

}