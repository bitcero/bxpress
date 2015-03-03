<{include file="db:bxpress-header.tpl"}>
<{include file="db:bxpress-announcements.tpl"}>
<{foreach item=catego from=$categos}>
<table class="outer bx_table" cellspacing="1" width="100%" style="margin-bottom: 10px;">
    <tr>
        <th colspan="5"><{$catego.title}></th>
    </tr>
    <tr class="head" align="center">
        <td align="left" colspan="2"><{$lang_forum}></td>
        <td><{$lang_topics}></td>
        <td><{$lang_posts}></td>
        <td align="left"><{$lang_lastpost}></td>
    </tr>
    <{foreach item=forum from=$catego.forums}>
        <tr>
        	<td width="26" align="center" valign="top" class="even"><img src="<{$xoops_url}>/modules/bxpress/images/normal<{if $forum.last.new}>withnew<{/if}>.png" alt="" /></td>
            <td class="even"><strong><a href="<{$forum.link}>"><{$forum.name}></a></strong><br /><{$forum.desc}></td>
            <td align="center" class="odd" style="border-left: 1px solid #DADADA; border-right: 1px solid #DADADA;"><{$forum.topics}></td>
            <td align="center" class="odd" style="border-right: 1px solid #DADADA;"><{$forum.posts}></td>
            <td class="even">
            <a href="topic.php?id=<{$forum.last.topic}>&amp;pid=<{$forum.last.id}>#p<{$forum.last.id}>"><{$forum.last.date}></a> <{$forum.last.by}>
            </td>
        </tr>
    <{/foreach}>
</table>
<{/foreach}>

<table class="outer" cellspacing="1" width="100%">
<tr class="foot">
        <td width="50%">
            <{$lang_lastuser}> <a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>"><{$user.uname}></a><br />
            <{$lang_regnum}> <strong><{$register_num}></strong><br />
            <{$lang_annum}> <strong><{$anonymous_num}></strong><br />
        </td>
        <td width="50%" align="right">
            <{$lang_totalusers}> <strong><{$total_users}></strong><br />
            <{$lang_totaltopics}> <strong><{$total_topics}></strong><br />
            <{$lang_totalposts}> <strong><{$total_posts}></strong>
        </td>
    </tr>
</table>

<{include file="db:system_notification_select.html"}>
