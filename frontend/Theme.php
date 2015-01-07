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

            /* Adding foundation */
            $themeRegistry->addCss(SiteConfig::themeLibrariessUrl() . "bootstrap/css/bootstrap.min.css");
            $themeRegistry->addScript(SiteConfig::themeLibrariessUrl() . "bootstrap/bootstrap.min.js");

            /* Adding Angular */
            $themeRegistry->addScript(SiteConfig::themeLibrariessUrl() . "angular/angular.min.js", 2);

            $themeRegistry->addCss(SiteConfig::themeCssUrl() . "style.css");
            $themeRegistry->addCss(array("file" => SiteConfig::themeCssUrl() . "print.css", "media" => "print"));
            
            $themeRegistry->addScript(SiteConfig::themeScriptsUrl() . "main.min.js", 20);
        }

        /**
         * @desc Formats the screen messages
         * @return The formatted screen messages
         */
        public static function getFormattedScreenMessages()
        {
            /* Get the messages from the screen messages class */
            $messages = ScreenMessage::getMessages();

            if (count($messages) < 1)
            {
                return false;
            }

            /* If there are messages, generate the ul */
            $template = new Template(SiteConfig::templatesPath() . "/inner/screen-messages");
            $template->messages = $messages;
            $template->message_count = count($messages);
            return $template->parse();
        }

    }
    