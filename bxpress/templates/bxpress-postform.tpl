<{include file="db:bxpress-header.tpl"}>

<{$topic_form}>

<{if $posts}>
<hr>
<p class="lead text-info"><{$lang_topicreview}></p>
<{foreach item=post from=$posts}>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <header><strong><{$post.uname}></strong><br><{$post.time}></header>
            <{$post.text}>
        </div>
    </div>
<{/foreach}>
<{/if}>