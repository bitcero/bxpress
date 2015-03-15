<{include file="db:bxpress-header.tpl"}>
<{include file="db:bxpress-announcements.tpl"}>

<{foreach item=catego from=$categos}>

    <{if $catego.forums}>
    <div class="bxpress-forums-list">

        <h3 class="list-title"><{$catego.title}></h3>

        <{foreach item=forum from=$catego.forums}>
            <div class="forum-item<{if $forum.active!=1}> forum-inactive<{/if}>">
                <div class="media">
                    <{if $forum.image}>
                    <div class="media-left">
                        <a href="<{$forum.link}>">
                            <img src="<{$forum.image}>" class="media-object forum-image" alt="<{$forum.name}>">
                        </a>
                    </div>
                    <{/if}>
                    <div class="media-body">
                        <h5 class="forum-name"><a href="<{$forum.link}>"><{$forum.name}></a></h5>
                        <{$forum.desc}>
                        <footer>
                            <{$lang_foot|sprintf:"<strong>`$forum.posts`</strong>":"<strong>`$forum.topics`</strong>":"`$forum.category.title`":"<img src='`$forum.last.user.avatar`'>":"`$forum.last.user.name`"|replace:"s=50":"s=18"}>
                        </footer>
                    </div>
                </div>
            </div>
        <{/foreach}>

    </div>
    <{/if}>
<{/foreach}>

<div class="row info-forums">
    <div class="col-sm-6">
        <{$lang_lastuser}> <a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>"><{$user.uname}></a><br />
        <{$lang_regnum}> <strong><{$register_num}></strong><br />
        <{$lang_annum}> <strong><{$anonymous_num}></strong><br />
    </div>
    <div class="col-sm-6 text-right">
        <{$lang_totalusers}> <strong><{$total_users}></strong><br />
        <{$lang_totaltopics}> <strong><{$total_topics}></strong><br />
        <{$lang_totalposts}> <strong><{$total_posts}></strong>
    </div>
</div>

<{include file="db:system_notification_select.html"}>
