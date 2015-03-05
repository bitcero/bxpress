<{include file="db:bxpress-header.tpl"}>
<{include file="db:bxpress-announcements.tpl"}>

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
                <{$topic.title}>
                <{if !$topic.approved}><a href="./moderate.php?id=<{$forum.id}>" >[<{$lang_noapproved}>]</a><{/if}>
            </li>
        </ol>
    </div>

</div>

<div class="text-right">
    <{$postsNavPage}>
</div>

<div class="bxpress-posts-list">

    <div class="row">
        <div class="col-sm-7">
            <h3 class="list-title"><{$topic.title}></h3>
        </div>
        <div class="col-sm-5 text-right">
            <{if $topic.closed}><span class="fa fa-lock"></span> <{$lang_topicclosed}><{/if}>
            <{if $can_reply && !$topic.closed}>
                <a href="post.php?tid=<{$topic.id}>" class="btn btn-success reply"><span class="fa fa-reply"></span> <{$lang_reply}></a>
            <{/if}>
            <{if $can_topic && !$topic.closed}>
                <a href="post.php?fid=<{$forum.id}>" class="btn btn-info newtopic"><span class="fa fa-comment"></span> <{$lang_newtopic}></a>
            <{/if}>
        </div>
    </div>

    <{foreach item=post from=$posts}>
        <{if $post.approved || $post.canshow}>
            <div class="post-item" id="p<{$post.id}>">

                <div class="media">
                    <div class="media-left">
                        <a href="<{$xoops_url}>/userinfo.php?uid=<{$post.poster.id}>">
                            <img src="<{$post.poster.avatar}>" class="media-object post-poster-avatar" alt="<{$post.poster.name}>">
                        <span class="poster-legend">
                            <{if $post.poster.type=='admin'}>
                                <{$lang_admin}>
                            <{elseif $post.poster.type=='moderator'}>
                                <{$lang_moderator}>
                            <{elseif $post.poster.type=='user'}>
                                <{$lang_user}>
                            <{else}>
                                <{$lang_anonymous}>
                            <{/if}>
                        </span>
                        </a>
                    </div>

                    <div class="media-body">
                        <header>
                            <a href="<{$post.poster.link}>"><{$post.poster.name}></a>
                            <span class="separator"><span class="fa fa-comment"></span> <{$post.replies}></span>
                        <span class="separator">
                            <a href="#" class="like-this-post"<{if $xoops_isuser}> data-post-id="<{$post.id}>"<{/if}>>
                                <span class="fa fa-heart<{if $post.likes_count<=0}> text-muted<{/if}>"></span>
                            </a>
                            <span class="likes-counter"><{$post.likes_count}></span>
                        </span>
                            <span class="separator"><span class="fa fa-clock-o"></span> <{$post.date}></span>
                            <{if $post.parent>0}>
                                <span class="separator"><span class="fa fa-reply"></span> <{$lang_inreply|sprintf:"<a href=\"topic.php?pid=`$post.parent`#p`$post.parent`\">`$lang_postnum`</a>"|sprintf:$post.parent}></span>
                            <{/if}>
                        </header>

                        <{$post.text}>

                        <{if $post.edit}>
                            <fieldset>
                                <legend><{$lang_edittext}></legend>
                                <table cellpadding="2" cellspacing="1" border="0">
                                    <tr>
                                        <td><{$post.edit}></td>
                                    </tr>
                                </table>
                            </fieldset>
                        <{/if}>

                        <{if $post.attachscount>0}>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="post-attachments">
                                        <strong><span class="fa fa-paperclip"></span> <{$lang_attachments}></strong>
                                        <{foreach item=a from=$post.attachs}>
                                            <img src="<{$a.icon}>" align="absmiddle" />
                                            <a href="files.php?id=<{$a.id}>&amp;topic=<{$topic.id}>"><{$a.title}></a> (<{$a.size}>)
                                        <{/foreach}>
                                    </div>
                                </div>
                            </div>
                        <{/if}>

                        <footer>
                            <ul class="list-inline pull-left">
                                <{if $can_reply && !$topic.closed}>
                                    <li>
                                        <a href="post.php?tid=<{$topic.id}>&amp;pid=<{$post.id}>"><span class="fa fa-reply"></span> Reply</a>
                                    </li>
                                <{/if}>
                                <{if $post.canreport}>
                                    <li>
                                        <a href="report.php?pid=<{$post.id}>&amp;op=report"><span class="fa fa-exclamation-triangle"></span> <{$lang_report}></a>
                                    </li>
                                <{/if}>
                                <{if $post.candelete}>
                                    <li>
                                        <a href="delete.php?id=<{$post.id}>"><span class="fa fa-times-circle"></span> <{$lang_delete}></a>
                                    </li>
                                <{/if}>
                                <{if $post.canedit}>
                                    <li>
                                        <a href="edit.php?id=<{$post.id}>"><span class="fa fa-pencil"></span> <{$lang_edit}></a>
                                    </li>
                                <{/if}>
                                <{if $post.canreport}>
                                    <li>
                                        <a href="post.php?tid=<{$topic.id}>&amp;quote=<{$post.id}>"><span class="fa fa-quote-left"></span> <{$lang_quote}></a>
                                    </li>
                                <{/if}>
                                <{if $post.edit || !$post.approved && $post.canedit}>
                                    <li>
                                        <a href="moderate.php?posts=<{$post.id}>&amp;id=<{$forum.id}>&amp;op=approvedpost&amp;XOOPS_TOKEN_REQUEST=<{$token}>"><span class="fa fa-check-square-o"></span> <{$lang_app}></a>
                                    </li>
                                <{elseif $post.edit || $post.approved && $post.canedit}>
                                    <li>
                                        <a href="moderate.php?posts=<{$post.id}>&amp;id=<{$forum.id}>&amp;op=noapprovedpost&amp;XOOPS_TOKEN_REQUEST=<{$token}>"><span class="fa fa-square-o"></span> <{$lang_noapp}></a>
                                    </li>
                                <{/if}>
                            </ul>

                            <div class="post-likes">
                                <{if $post.likes}>
                                    <{$lang_likedby}>
                                    <{foreach item=like from=$post.likes}>
                                        <a href="<{$xoops_url}>/userinfo.php?uid=<{$like.uid}>" data-user="<{$like.uid}>" data-toggle="tooltip" title="<{$like.name}>">
                                            <img src="<{$like.avatar|replace:"s=50":"s=40"}>" alt="<{$like.name}>">
                                        </a>
                                    <{/foreach}>
                                    <span class="likes-more">
                                        <{if $post.likes_count>3}>
                                            <{$lang_likedmore|sprintf:$post.likes_count-3}>
                                        <{/if}>
                                    </span>
                                <{/if}>
                            </div>
                        </footer>
                    </div>

                </div>
            </div>
        <{/if}>
    <{/foreach}>
</div>

<div class="row">
    <div class="col-sm-5">
        <{$postsNavPage}>
    </div>
    <div class="col-sm-7 text-right">
        <{if $can_reply || $topic.closed}>
            <{if $topic.closed}><span class="label label-warning"><span class="fa fa-lock"></span> <{$lang_topicclosed}></span><{/if}>
            <{if $can_reply && !$topic.closed}>
                <a href="post.php?tid=<{$topic.id}>" class="btn btn-success reply"><span class="fa fa-reply"></span> <{$lang_reply}></a>
            <{/if}>
            <{if $can_topic && !$topic.closed}>
                <a href="post.php?fid=<{$forum.id}>" class="btn btn-info newtopic"><span class="fa fa-comment"></span> <{$lang_newtopic}></a>
            <{/if}>
        <{/if}>
    </div>
</div>

<hr>

<div class="row bxpress-options">
    <div class="col-sm-5">
        <form name="frmGo" method="get" action="forum.php" style="margin: 0;">
            <div class="input-group">
                <span class="input-group-addon"><{$lang_goto}></span>
                <select name="id" onchange="submit();" class="form-control">
                    <{foreach item=foro from=$forums}>
                        <option value="<{$foro.id}>"<{if $foro.id==$forum.id}> selected="selected"<{/if}>><{$foro.title}></option>
                    <{/foreach}>
                </select>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-info"><{$lang_go}></button>
                </span>
            </div>
        </form>
    </div>

    <div class="col-sm-7 text-right">
        <{if $forum.moderator}>
            <a href="moderate.php?op=move&amp;topics=<{$topic.id}>&amp;id=<{$forum.id}>" class="btn btn-info"><span class="fa fa-arrows"></span> <{$lang_move}></a>
            <{if $topic.closed}>
                <a href="moderate.php?op=open&amp;topics=<{$topic.id}>&amp;id=<{$forum.id}>" class="btn btn-warning"><span class="fa fa-unlock"></span> <{$lang_open}></a>
            <{else}>
                <a href="moderate.php?op=close&amp;topics=<{$topic.id}>&amp;id=<{$forum.id}>" class="btn btn-warning"><span class="fa fa-lock"></span> <{$lang_close}></a>
            <{/if}>

            <{if $topic.sticky}>
                <a href="moderate.php?op=unsticky&amp;topics=<{$topic.id}>&amp;id=<{$forum.id}>" class="btn btn-danger"><{$lang_unsticky}></a>
            <{else}>
                <a href="moderate.php?op=sticky&amp;topics=<{$topic.id}>&amp;id=<{$forum.id}>" class="btn btn-danger"><span class="fa fa-thumb-tack"></span> <{$lang_sticky}></a>
            <{/if}>
        <{/if}>
    </div>
</div>
<input type="hidden" id="bxpress-token" value="<{$bxpress_token}>">
<{include file="db:system_notification_select.html"}>