<div class="bxpress-counters">
    <ul class="list-group">
        <{foreach item=counter from=$block.counters}>
            <li class="list-group-item">
                <span class="number"><{$counter.count}></span>
                <span class="caption"><{$counter.caption}></span>
            </li>
        <{/foreach}>
    </ul>
</div>