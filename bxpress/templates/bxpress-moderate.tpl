<{include file="db:bxpress-header.tpl"}>

<div class="bxpress-options row">
    <div class="col-xs-12">
        <ol class="breadcrumb">
            <li>
                <a href="./"><span class="fa fa-home"></span> <{$forums_title}></a>
            </li>
            <li>
                <a href="forum.php?id=<{$forum.id}>"><{$forum.title}></a>
            </li>
            <li class="active">
                <{$lang_moderating}>
            </li>
        </ol>
    </div>
</div>

<{$itemsNavPage}>

<h3><{$forum.title}></h3>

<form name="frmTopics" method="post" action="moderate.php">
    <div class="table-responsive">
        <table class="table table-striped table-bordered moderator-list" cellspacing="1" width="100%">
            <tr class="head" align="center">
                <td width="20"><input type="checkbox" name="checkall" onchange="xoopsCheckAll('frmTopics','checkall');" /></td>
                <td colspan="2" align="left"><{$lang_topic}></td>
                <td><{$lang_replies}></td>
                <td><{$lang_views}></td>
                <td><{$lang_approved}></td>
                <td align="left"><{$lang_lastpost}></td>
            </tr>
            <{foreach item=topic from=$topics}>
                <tr class="<{cycle values="even,odd"}>">
                    <td align="center"><input type="checkbox" name="topics[]" value="<{$topic.id}>" /></td>
                    <td width="26" align="center" class="indicators">
                        <{if $topic.sticky}>
                            <span class="fa fa-thumb-tack sticky"></span>
                        <{/if}>
                        <{if $topic.popular}>
                            <span class="fa fa-line-chart popular"></span>
                        <{/if}>
                        <{if $topic.last.new}>
                            <span class="fa fa-clock-o new"></span>
                        <{/if}>
                    </td>
                    <td class="lighter">
                        <{if $topic.closed}>
                            <img src="images/lock.png" align="absmiddle" alt="" />
                        <{elseif $topic.sticky}>
                            <{$lang_sticky}>
                        <{/if}>
                        <strong><a href="topic.php?id=<{$topic.id}>"><{$topic.title}></a></strong>
                        <{$topic.by}>
                        <{if $topic.last.new}>
                            [ <a href="topic.php?id=<{$topic.id}>&amp;op=new"><{$lang_newposts}></a> ]
                        <{/if}>
                        <{if $topic.tpages>1}>&nbsp;
                            [<{foreach item=page from=$topic.pages}>
                            <{if $page!='...'}>
                                <a href="topic.php?id=<{$topic.id}>&amp;pag=<{$page}>"><{$page}></a>
                            <{else}>
                                ...
                            <{/if}>
                        <{/foreach}>]
                        <{/if}>
                    </td>
                    <td align="center"><{$topic.replies}></td>
                    <td align="center"><{$topic.views}></td>
                    <td align="center"><{if $topic.approved}><img src="images/ok.png" border="0" /><{else}><img src="images/no.png" border="0" /><{/if}>
                    <td class="lighter">
                        <a href="topic.php?id=<{$topic.id}>&amp;pid=<{$topic.last.id}>#post<{$topic.last.id}>"><{$topic.last.date}></a> <{$topic.last.by}>
                    </td>
                </tr>
            <{/foreach}>
            <tr class="foot">
                <td align="right">&nbsp;</td>
                <td colspan="6">
                    <strong>
                        <a href="#" onclick="document.forms['frmTopics'].op.value='move'; document.forms['frmTopics'].submit();"><{$lang_move}></a> |
                        <a href="#" onclick="document.forms['frmTopics'].op.value='delete'; if(confirm('<{$lang_confirm}>')) document.forms['frmTopics'].submit();"><{$lang_delete}></a> |
                        <a href="#" onclick="document.forms['frmTopics'].op.value='open'; document.forms['frmTopics'].submit();"><{$lang_open}></a> |
                        <a href="#" onclick="document.forms['frmTopics'].op.value='close'; document.forms['frmTopics'].submit();"><{$lang_close}></a> |
                        <a href="#" onclick="document.forms['frmTopics'].op.value='sticky'; document.forms['frmTopics'].submit();"><{$lang_dosticky}></a> |
                        <a href="#" onclick="document.forms['frmTopics'].op.value='unsticky'; document.forms['frmTopics'].submit();"><{$lang_dounsticky}></a> |
                        <a href="#" onclick="document.forms['frmTopics'].op.value='approved'; document.forms['frmTopics'].submit();"><{$lang_app}></a> |
                        <a href="#" onclick="document.forms['frmTopics'].op.value='noapproved'; document.forms['frmTopics'].submit();"><{$lang_noapp}></a>
                    </strong>
                </td>
            </tr>
        </table>
    </div>
<input type="hidden" name="op" value="" />
<input type="hidden" name="id" value="<{$forum.id}>" />
<{$token_input}>
</form>

<{$itemsNavPage}>
