<?php if (!System\Classes\PluginManager::instance()->hasPlugin('October.Drivers')): ?>
    <div class="callout callout-warning fade in no-subheader">
        <div class="header">
            <i class="icon-exclamation-circle"></i>
            <h3><?= __("Drivers Not Installed") ?></h3>
        </div>
        <div class="content">
            <p><?= e(__("This mail method requires the plugin ':plugin' be installed before you can send mail.", ['plugin' => 'October.Drivers'])) ?></p>
        </div>
    </div>
<?php endif ?>
