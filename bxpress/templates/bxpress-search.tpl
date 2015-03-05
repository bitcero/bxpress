<{include file="db:bxpress-header.tpl"}>
<{include file="db:bxpress-announcements.tpl"}>

<form name="frmsearch" method="GET"  action="search.php">
<table class="outer">
	<tr class="odd">
		<td><strong><{$lang_search}></strong>
		    <input type="text" name="search" size="20" value="<{$search}>" >
		    <label><input type="radio" name="type" value="0"<{if $type==0 }> checked="checked"<{/if}> /><{$lang_allwords}></label>
		    <label><input type="radio" name="type" value="1"<{if $type==1 }> checked="checked"<{/if}> /><{$lang_anywords}></label>
		    <label><input type="radio" name="type" value="2"<{if $type==2 }> checked="checked"<{/if}> /><{$lang_exactphrase}> </label>
		    <input class="formButton" type="submit" name="sbt" value="<{$lang_search}>" />
		</td>
	</tr>
	
	<tr class="odd">
		<td>
		    <input type="radio" name="themes" value="0" <{if $themes==0 }>checked="checked"<{/if}> /><{$lang_alltopics}>
		    <input type="radio" name="themes" value="1" <{if $themes==1 }>checked="checked"<{/if}> /><{$lang_recenttopics}>
		    <input type="radio" name="themes" value="2" <{if $themes==2 }>checked="checked"<{/if}> /><{$lang_anunswered}>
		</td>
	</tr>

</table>
</form>
<br />
<{$itemsNavPage}>

<div class="table-responsive">
    <table class="table table-hover results-list">
        <head>
            <tr class="head" align="center">
                <td>&nbsp;</td>
                <th align="left"><{$lang_topic}></th>
                <th class="text-center"><{$lang_forum}></th>
                <th class="text-center"><{$lang_replies}></th>
                <th class="text-center"><{$lang_views}></th>
                <th class="text-center"><{if $themes==0 && $search==''}><{$lang_last}><{else}><{$lang_date}><{/if}></th>
            </tr>
        </head>
        <tbody>
        <{foreach item=topic from=$posts}>

            <tr class="bxpress_listing">
                <td width="30">
                    <{if $topic.sticky}><span class="fa fa-thumb-tack"></span><{/if}>
                    <{if $topic.popular}><span class="fa fa-line-chart"></span><{/if}>
                    <{if $topic.last.new}><span class="fa fa-clock-o"></span><{/if}>
                <td>
                    <{if $topic.closed}>
                        <span class="fa fa-lock"></span>
                    <{elseif $topic.sticky}>
                        <{$lang_sticky}>
                    <{/if}>
                    <strong class="title"><{if $themes==0 && $search==''}><a href="topic.php?pid=<{$topic.firstpost}>#p<{$topic.firstpost}>"><{$topic.title}></a><{else}><a href="topic.php?pid=<{$topic.id_post}>#p<{$topic.id_post}>"><{$topic.title}></a><{/if}></strong>
                    <span class="by"><{$topic.by}></span>
                    <{if $topic.last.new}>
                        [ <a href="topic.php?id=<{$topic.id}>&amp;op=new"><{$lang_newposts}></a> ]
                    <{/if}>
                    <{if $topic.tpages>1}>&nbsp;
                        <span class="pages">
			[<{foreach item=page from=$topic.pages}>
                            <{if $page!='...'}>
                                <a href="topic.php?id=<{$topic.id}>&amp;pag=<{$page}>"><{$page}></a>
                            <{else}>
                                ...
                            <{/if}>
                            <{/foreach}>]
                        </span>
                    <{/if}>
                    <br />
                    <span class="text"><{$topic.post_text}></span>
                </td>
                <td class="text-center"><{$topic.forum}></td>
                <td class="text-center"><{$topic.replies}></td>
                <td class="text-center"><{$topic.views}></td>
                <td class="text-center"><{if $search=='' && $themes==0}><a href="topic.php?pid=<{$topic.last.id}>#p<{$topic.last.id}>"><{$topic.last.date}></a> <{$topic.last.by}><{else}><{$topic.date}><{/if}></td>
            </tr>

        <{/foreach}>
        </tbody>
    </table>
</div>

<{$itemsNavPage}>