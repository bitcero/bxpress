<div class="bxpress-block-users">
    <ol>
        <{foreach item=user from=$block.users}>
            <li>
                <img class="avatar" src="<{$user.avatar|replace:"s=80":"s=40"}>" alt="<{$user.name}>">
                <a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>"><{$user.name}></a>
                <span class="separator">
                    <span class="fa fa-comment"></span> <{$user.posts}>
                </span>
                <span class="separator">
                    <span class="fa fa-heart"></span> <{$user.likes}>
                </span>
            </li>
        <{/foreach}>
    </ol>
</div>