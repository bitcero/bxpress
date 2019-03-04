<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1.0', {'packages':['corechart']});
function drawVisualization(w,h) {
      // Create and populate the data table.
      var data = new google.visualization.DataTable();
      data.addColumn('string','x');
      <?php foreach ($forums as $f): ?>
      data.addColumn('number', '<?php echo $f->name(); ?>');
      <?php endforeach; ?>
      <?php foreach ($days_rows as $r): ?>
      data.addRow(<?php echo $r; ?>);    
      <?php endforeach; ?>

      // Create and draw the visualization.
      new google.visualization.LineChart(document.getElementById('activity')).
          draw(data, {curveType: "none",
                        legend: 'none',
                      width: w, height: h,
                      vAxis: {maxValue: <?php echo $max; ?>},
                      chartArea:{left:30,top:20,height:"90%"},
              series: {
                  0: {color: '#03A9F4', lineWidth: 4}
              }
                      }
              );
}
</script>
<h1 class="cu-section-title"><?php _e('Dashboard', 'bxpress'); ?></h1>
<script type="text/javascript">
    var xoops_url = '<?php echo XOOPS_URL; ?>';
</script>

<div class="row" data-news="load" data-boxes="load" data-module="bxpress" data-target="#bxpress-news" data-box="bxpress-dashboard" data-container="dashboard">

    <div class="size-1" data-dashboard="item">
        <div class="cu-box">
            <div class="box-header">
                <h3 class="box-title"><?php _e('Overview', 'bxpress'); ?></h3>
            </div>
            <div class="box-content">
                <ul class="bxpress-overview">
                    <li>
                        <a href="categories.php">
                            <span class="fa fa-folder">
                                <span class="badge"><?php echo $catnum; ?></span>
                            </span>
                            <h5><?php _e('Categories', 'bxpress'); ?></h5>
                        </a>
                    </li>
                    <li>
                        <a href="forums.php">
                            <span class="fa fa-comments">
                                <span class="badge"><?php echo $forumnum; ?></span>
                            </span>
                            <h5><?php _e('Forums', 'bxpress'); ?></h5>
                        </a>
                    </li>
                    <li>
                        <span>
                            <span class="fa fa-comments-o">
                                <span class="badge"><?php echo $topicnum; ?></span>
                            </span>
                            <h5><?php echo _e('Topics', 'bxpress'); ?></h5>
                        </span>
                    </li>
                    <li>
                        <span>
                            <span class="fa fa-reply">
                                <span class="badge"><?php echo $postnum; ?></span>
                            </span>
                            <h5><?php _e('Posts', 'bxpress'); ?></h5>
                        </span>
                    </li>
                    <li>
                        <a href="announcements.php">
                            <span class="fa fa-bullhorn">
                                <span class="badge"><?php echo $annum; ?></span>
                            </span>
                            <h5><?php _e('Announcements', 'bxpress'); ?></h5>
                        </a>
                    </li>
                    <li>
                        <span>
                            <span class="fa fa-paperclip">
                                <span class="badge"><?php echo $attnum; ?></span>
                            </span>
                            <h5><?php _e('Files', 'bxpress'); ?></h5>
                        </span>
                    </li>
                    <li>
                        <a href="reports.php">
                            <span class="fa fa-exclamation bg-warning">
                                <span class="badge"><?php echo $repnum; ?></span>
                            </span>
                            <h5><?php _e('Reports', 'bxpress'); ?></h5>
                        </a>
                    </li>
                    <li>
                        <span>
                            <span class="fa fa-heart">
                                <span class="badge"><?php echo $likes_num; ?></span>
                            </span>
                            <h5><?php _e('Likes', 'bxpress'); ?></h5>
                        </span>
                    </li>
                    <li>
                        <span>
                            <span class="fa fa-calendar">
                                <span class="badge"><?php echo $daysnum; ?></span>
                            </span>
                            <h5><?php _e('Days', 'bxpress'); ?></h5>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="size-2" data-dashboard="item">
        <div class="cu-box box-light-blue">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3 class="box-title"><?php _e('Activity', 'bxpress'); ?></h3>
            </div>
            <div class="box-content">
                <div id="activity"></div>
            </div>
        </div>
    </div>

    <div class="size-1" data-dashboard="item">
        <div class="cu-box box-default">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3 class="box-title"><?php _e('Recent Topics', 'bxpress'); ?></h3>
            </div>
            <div class="box-content">
                <ul class="recent-posts">
                <?php foreach ($topics as $t): ?>
                    <li>
                        <strong><a href="<?php echo $t['link']; ?>"><?php echo $t['title']; ?></a></strong>
                            <span class="tdata">
                            <?php echo sprintf(__('Forum: %s', 'bxpress'), '<a href="' . $t['forum']['link'] . '">' . $t['forum']['name'] . '</a>'); ?><br>
                                <?php echo $t['post']['date']; ?> |
                            <em><a target="_blank" href="<?php echo XOOPS_URL; ?>/userinfo.php?uid=<?php echo $t['post']['uid']; ?>"><?php echo $t['post']['by']; ?></a></em>
                            </span>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="size-1" data-dashboard="item">
        <div class="cu-box box-red">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3 class="box-title"><?php _e('Popular Topics', 'bxpress'); ?></h3>
            </div>
            <div class="box-content">
                <ul class="recent-posts">
                    <?php foreach ($poptops as $t): ?>
                        <li>
                            <strong><a href="<?php echo $t['link']; ?>"><?php echo $t['title']; ?></a></strong>
                            <span class="tdata">
                            <?php echo sprintf(__('Forum: %s', 'bxpress'), '<a href="' . $t['forum']['link'] . '">' . $t['forum']['name'] . '</a>'); ?><br>
                                <?php echo $t['date']; ?> | <?php echo sprintf(__('Replies: %s', 'bxpress'), '<strong>' . $t['replies'] . '</strong>'); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="size-1" data-dashboard="item">
        <div class="cu-box">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3 class="box-title"><?php _e('bXpress News', 'bxpress'); ?></h3>
            </div>
            <div class="box-content" id="bxpress-news">

            </div>
        </div>
    </div>

</div>
