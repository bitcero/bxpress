<header class="row bx-header">
    <div class="col-xs-12">
        <h2><{$forums_title}></h2>
        <div class="row">
            <div class="col-sm-8">

                <ul class="list-inline bx-options">
                    <li>
                        <a href="./"><span class="fa fa-caret-right"></span> <{$lang_index}></a>
                    </li>
                    <{if $can_search}>
                        <li>
                            <a href="search.php"><span class="fa fa-caret-right"></span> <{$lang_search}></a>
                        </li>
                    <{/if}>
                </ul>

            </div>
            <div class="col-sm-4">

                <{if $can_search}>
                    <form name="frmSearch" method="get" action="search.php" style="margin: 0">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="<{$lang_search_ph}>">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit"><span class="fa fa-search"></span></button>
                            </span>
                        </div>
                    </form>
                <{/if}>

            </div>
        </div>

    </div>
</header>
