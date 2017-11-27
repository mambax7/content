<style type="text/css">
@import url("<{$xoops_url}>/modules/content/assets/css/style.css");
</style>
<ul id="tabmenu">
<{foreach item=tab from=$tabs}>
<li>
<{if $tab.storyid == $id}>
<a class="active" href="index.php?id=<{$tab.storyid}>"><{$tab.title}></a>
<{else}>
<{if $tab.storyid != $id}>
<a href="index.php?id=<{$tab.storyid}>"><{$tab.title}></a>
<{/if}><{/if}>
</li>
<{/foreach}>
</ul>

<!-- End item loop -->
<div id="tabcontent">
<div style="padding:10px;">
		<{if $header_image neq ''}>
			<img src="<{$xoops_url}>/modules/content/headers/<{$header_image}>">
		<{else}>
			<h2><{$title}></h2>
		<{/if}>
</div>
     <{$content}>

</div>

<div class="printandemail"style="text-align:left;display:inline-block;vertical-align:middle;padding-left:10px;width:49%;">
<a href="print.php?id=<{$id}>" target="_new">
<img src="<{$xoops_url}>/modules/content/assets/images/print.png" alt="print" style="border:0;padding-right:5px;" />
</a>
<a href="<{$mail_link}>">
<img src="<{$xoops_url}>/modules/content/assets/images/email.png" alt="email" style="border:0;" />
</a>
</div>
<{if $xoops_isadmin == 1}>
<div style="text-align:right;display:inline-block;vertical-align:middle;width:49%;">
<a href="<{$xoops_url}>/modules/content/admin/add_content.php?id=<{$id}>&return=1&showshort=1">
<img src="<{$xoops_url}>/modules/content/assets/images/add.png" alt="add" style="border:0;padding-right:5px;" />
</a>
<a href="<{$xoops_url}>/modules/content/edit_content.php?id=<{$id}>&return=1&showshort=1">
<img src="<{$xoops_url}>/modules/content/assets/images/edit.png" alt="edit" style="border:0;" />
</a>
</div>
<{/if}>
 
<{if $nocomments == 0}>
<br><br>
<div style="text-align: center; padding: 3px; margin: 3px;">
  <{$commentsnav}>
  <{$lang_notice}>
</div>
<div style="margin: 3px; padding: 3px;">
<!-- start comments loop -->
<{if $comment_mode == "flat"}>
  <{include file="db:system_comments_flat.tpl"}>
<{elseif $comment_mode == "thread"}>
  <{include file="db:system_comments_thread.tpl"}>
<{elseif $comment_mode == "nest"}>
  <{include file="db:system_comments_nest.tpl"}>
<{/if}>
<!-- end comments loop -->
</div>
<{/if}>