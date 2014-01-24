<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1.0', {'packages':['corechart']});
function drawVisualization(w,h) {
      // Create and populate the data table.
      var data = new google.visualization.DataTable();
      data.addColumn('string','x');
      <?php foreach($forums as $f): ?>
      data.addColumn('number', '<?php echo $f->name(); ?>');
      <?php endforeach; ?>
      <?php foreach($days_rows as $r): ?>
      data.addRow(<?php echo $r; ?>);    
      <?php endforeach; ?>

      // Create and draw the visualization.
      new google.visualization.LineChart(document.getElementById('activity')).
          draw(data, {curveType: "none",
                      width: w, height: h,
                      vAxis: {maxValue: <?php echo $max; ?>},
                      chartArea:{left:30,top:20,height:"90%"}
                      }
              );
}
</script>
<h1 class="cu-section-title"><?php _e('Dashboard','bxpress'); ?></h1>
<script type="text/javascript">
    var xoops_url = '<?php echo XOOPS_URL; ?>';
</script>

<div class="row">

    <div class="col-md-4">

        <div class="cu-box">
            <div class="box-header">
                <h3><?php _e('Overview','bxpress'); ?></h3>
            </div>
            <div class="box-content">
                <ul class="list-unstyled overvitem">
                    <li>
                        <a href="categos.php"><?php echo sprintf(__('%s Categories','bxpress'), '<strong>'.$catnum.'</strong>'); ?></a>
                    </li>
                    <li>
                        <a href="forums.php"><?php echo sprintf(__('%s Forums Available','bxpress'), '<strong>'.$forumnum.'</strong>'); ?></a>
                    </li>
                    <li>
                        <?php echo sprintf(__('%s Topics Created','bxpress'), '<strong>'.$topicnum.'</strong>'); ?>
                    </li>
                    <li>
                        <?php echo sprintf(__('%s Posts Sent','bxpress'), '<strong>'.$postnum.'</strong>'); ?>
                    </li>
                    <li>
                        <a href="announcements.php"><?php echo sprintf(__('%s Announcements Made','bxpress'), '<strong>'.$annum.'</strong>'); ?></a>
                    </li>
                    <li>
                        <?php echo sprintf(__('%s Files Attached','bxpress'), '<strong>'.$attnum.'</strong>'); ?>
                    </li>
                    <li>
                        <a href="reports.php"><?php echo sprintf(__('%s Reports Received','bxpress'), '<strong>'.$repnum.'</strong>'); ?></a>
                    </li>
                    <li>
                        <?php echo sprintf(__('%s Days Running','bxpress'), '<strong>'.$daysnum.'</strong>'); ?>
                    </li>
                </ul>
            </div>
        </div>

        <div class="cu-box" data-load="news" data-module="bxpress" data-target="#bxpress-news">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3><?php _e('bXpress News','bxpress'); ?></h3>
            </div>
            <div class="box-content" id="bxpress-news">

            </div>
        </div>

    </div>

    <!-- Activity -->
    <div class="col-md-5">

        <div class="cu-box">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3><?php _e('Activity','bxpress'); ?></h3>
            </div>
            <div class="box-content">
                <ul id="activity-options">
                    <li class="activity pressed"><?php _e('Last 30 days','bxpress'); ?></li>
                    <li class="recent"><?php _e('Recent Posts in Topics','bxpress'); ?></li>
                    <li class="topten"><?php _e('Popular Topics','bxpress'); ?></li>
                </ul>
                <div id="activity"></div>
                <div id="recent">
                    <?php foreach($topics as $t): ?>
                        <div class="<?php echo tpl_cycle("even,odd"); ?>">
                            <strong><a href="<?php echo $t['link']; ?>"><?php echo $t['title']; ?></a></strong>
                            <span class="tdata">
                            <?php echo sprintf(__('Forum: %s','bxpress'), '<a href="'.$t['forum']['link'].'">'.$t['forum']['name'].'</a>'); ?><br />
                                <?php echo $t['post']['date']; ?> |
                            <em><a target="_blank" href="<?php echo XOOPS_URL; ?>/userinfo.php?uid=<?php echo $t['post']['uid']; ?>"><?php echo $t['post']['by']; ?></a></em>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="topten">
                    <?php foreach($poptops as $t): ?>
                        <div class="<?php echo tpl_cycle("even,odd"); ?>">
                            <strong><a href="<?php echo $t['link']; ?>"><?php echo $t['title']; ?></a></strong>
                            <span class="tdata">
                            <?php echo sprintf(__('Forum: %s','bxpress'), '<a href="'.$t['forum']['link'].'">'.$t['forum']['name'].'</a>'); ?><br />
                                <?php echo $t['date']; ?> | <?php echo sprintf(__('Replies: %s','bxpress'), '<strong>'.$t['replies'].'</strong>'); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php RMEvents::get()->run_event("bxpress.dashboard.left.blocks"); ?>

    </div>
    <!--// Activity -->

    <!-- Other Info -->
    <div class="col-md-3">

        <div class="cu-box">
            <div class="box-header">
                <h3><i class="fa fa-thumbs-up"></i> <?php _e('Support my Work','dtransport'); ?></h3>
            </div>
            <div class="box-content support-me">
                <img class="avatar" src="http://www.gravatar.com/avatar/<?php echo $myEmail; ?>?s=80" alt="Eduardo Cortés (bitcero)" />
                <p><?php _e('Do you like my work? Then maybe you want support me to continue developing new modules.','dtransport'); ?></p>
                <?php echo $donateButton; ?>
            </div>
        </div>

        <div data-load="boxes"></div>

    </div>
    <!--// Other info -->

</div>
