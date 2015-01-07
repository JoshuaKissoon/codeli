<?php

    /**
     * A general class containing the main methods for the theming system to work with everything else
     * 
     * @author Joshua Kissoon
     * @date 20150101
     */
    class Theme implements CodeliTheme
    {

        /**
         * Add the theme's libraries and scripts 
         */
        public function init()
        {
            $themeRegistry = Codeli::getInstance()->getThemeRegistry();
            
            /* JQuery */
            $themeRegistry->addScript(SiteConfig::themeLibrariessUrl() . "jquery/jquery-2.1.1.min.js");
            
            /* Adding Angular */
            $themeRegistry->addScript(SiteConfig::themeLibrariessUrl() . "angular/angular.min.js", 1, true);

            /* Adding Bootstrap */
            $themeRegistry->addCss(SiteConfig::themeLibrariessUrl() . "bootstrap/css/bootstrap.min.css");
            $themeRegistry->addScript(SiteConfig::themeLibrariessUrl() . "bootstrap/js/bootstrap.min.js");


            $themeRegistry->addCss(SiteConfig::themeCssUrl() . "style.css");
            $themeRegistry->addCss(array("file" => SiteConfig::themeCssUrl() . "print.css", "media" => "print"));
            
            $themeRegistry->addScript(SiteConfig::themeScriptsUrl() . "main.min.js", 20);
            
            /* Our Angular JS Files */
            $themeRegistry->addScript(SystemConfig::frontendURL() . "ng-app/Data.js", 1);
            $themeRegistry->addScript(SystemConfig::frontendURL() . "ng-app/app.js", 2);
            $themeRegistry->addScript(SystemConfig::frontendURL() . "ng-app/main_controller.js");
        }
    }
    