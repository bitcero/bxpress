<div class="bxpress-block-topics">

    <{if $block.format=='full' || $block.format=='medium'}>

        <ol class="full">
            <{foreach item=topic from=$block.topics}>
                <li>
                    <strong><a href="<{$topic.link}>"><{$topic.title}></a></strong>
                    <div class="details">

                        <{if $block.format=='full'}>
                            <span class="separator">
                                <{$topic.updated}>
                            </span>
                        <{/if}>
                        <span class="separator">
                            <span class="fa fa-comment"></span> <{$topic.replies}>
                        </span>
                        <span class="separator">
                            <span class="fa fa-heart<{if $topic.likes>0}> text-danger<{/if}>"></span> <{$topic.likes}>
                        </span>
                        <span class="separator">
                            <span class="fa fa-eye"></span> <{$topic.hits}>
                        </span>
                        <{if $block.format=='medium'}>
                        <span class="separator">
                            <span class="fa fa-refresh"></span> <{$topic.updated}>
                        </span>
                        <{/if}>

                    </div>
                </li>
            <{/foreach}>
        </ol>

    <{else}>

        <ol>
            <{foreach item=topic from=$block.topics}>
                <li>
                    <a href="<{$topic.link}>"><{$topic.title}></a>
                </li>
            <{/foreach}>
        </ol>

    <{/if}>

</div>