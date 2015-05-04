<{include file="db:bxpress-header.tpl"}>
<{include file="db:bxpress-announcements.tpl"}>

<div class="bxpress-options row">
    <div class="col-sm-7">
        <ol class="breadcrumb">
            <li>
                <a href="./"><span class="fa fa-home"></span> <{$forums_title}></a>
            </li>
            <li class="active">
                <{$forum.title}>
            </li>
        </ol>
    </div>
    <div class="col-sm-5 text-right">
        <{if $can_topic}>
        <a href="post.php?fid=<{$forum.id}>" class="btn btn-success"><span class="fa fa-plus"></span> <{$lang_newtopic}></a>
        <{/if}>
        <{if $forum.moderator}>
            <a href="moderate.php?id=<{$forum.id}>" class="btn btn-warning"><span class="fa fa-gavel"></span>  <{$lang_moderate}></a>
        <{/if}>
    </div>
</div>

<{$itemsNavPage}>

<div class="bxpress-topics-list">
    <h3 class="list-title"><{$forum.title}></h3>

    <{foreach item=topic from=$topics}>
    <div class="topic-item<{if $topic.sticky}> sticky<{/if}>">
        <div class="media">
            <div class="media-left">
                <a href="<{$topic.link}>">
                    <img src="<{$topic.poster.avatar}>" class="media-object topic-poster-avatar" alt="<{$topic.title}>">
                    <span class="poster-legend">
                        <{if $topic.poster.type=='admin'}>
                            <{$lang_admin}>
                        <{elseif $topic.poster.type=='moderator'}>
                            <{$lang_moderator}>
                        <{else}>
                            <{$lang_user}>
                        <{/if}>
                    </span>
                </a>
            </div>
            <div class="media-body">
                <h5 class="topic-title"><a href="topic.php?id=<{$topic.id}>"><{$topic.title}></a></h5>
                <footer>
                    <span class="separator"><{$lang_updated|sprintf:$topic.last.date}></span>
                    <{if $topic.replies>0}>
                        <span class="separator"><{$lang_lastreply_by|sprintf:"<a href=\"topic.php?pid=`$topic.last.id`#p`$topic.last.id`\">`$lang_lastreply`</a>":"<strong>`$topic.last.poster.name`</strong>"}></span>
                        <span class="separator"><span class="fa fa-comment"></span> <{$topic.replies}></span>
                    <{else}>
                        <span class="separator"><{$lang_noreplies}></span>
                    <{/if}>
                    </span>
                    <span class="separator"><span class="fa fa-eye"></span> <{$topic.views}></span>
                </footer>
                <span class="topic-indicators"<{if !$topic.closed && !$topic.popular && !$topic.last.new}>style="display: none;"<{/if}>>
                    <{if $topic.closed}><span class="fa fa-lock closed bg-warning"></span><{/if}>
                    <{if $topic.popular}><span class="fa fa-line-chart popular bg-info"></span><{/if}>
                    <{if $topic.last.new}><span class="fa fa-clock-o new bg-success"></span><{/if}>
                </span>
            </div>
        </div>
    </div>
    <{/foreach}>
</div>

<{$itemsNavPage}>

<div class="row bxpress-options">
    <div class="col-sm-4">
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

    <div class="col-sm-8 text-right">
        <{if $can_topic}>
            <a href="post.php?fid=<{$forum.id}>" class="btn btn-success newtopic"><span class="fa fa-plus"></span> <{$lang_newtopic}></a>
        <{/if}>
        <{if $forum.moderator}>
            <a href="moderate.php?id=<{$forum.id}>" class="btn btn-warning"><span class="fa fa-gavel"></span> <{$lang_moderate}></a>
        <{/if}>
    </div>
</div>

<{$notifications}>