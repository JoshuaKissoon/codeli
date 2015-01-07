<!DOCTYPE html>
<html lang="en" class="ng-app:CodeliApp" id="ng-app" ng-app="CodeliApp" >

    <head>
        <?php if (isset($title)): ?>
                <title>
                    <?= $title; ?>
                </title>
            <?php endif; ?>

        <meta charset="UTF-8">
        <meta name="HandheldFriendly" content="true" />
        <meta name="MobileOptimized" content="320" />
        <meta name="Viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <!-- Adding Stylesheets -->
        <?php if (isset($stylesheets)): ?>
                <?= $stylesheets; ?>
            <?php endif; ?>

        <!--Adding Header Scripts-->
        <?php if (isset($header_scripts)): ?>
                <?= $header_scripts; ?>
            <?php endif; ?>

        <!--Other head data-->
        <?php if (isset($head)): ?>
                <?php print $head; ?>
            <?php endif; ?>
    </head>

    <body class="<?php print implode(" ", JPath::urlArgs()); ?>">
        <section id="status-messages">
            
        </section>

        <div ng-include src="'frontend/templates/main.html'"></div>

        <!--Adding Footer Scripts-->
        <?php if (isset($footer_scripts)): ?>
                <?= $footer_scripts; ?>
            <?php endif; ?>
    </body>
</html>