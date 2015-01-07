<?php

    /**
     * Class that is used to handle the current site URL
     * 
     * @author Joshua Kissoon
     * @since 20121219
     * @updated 20140623
     */
    class JPath
    {

        /**
         * @return The relative URL from which the request came from
         */
        public static function requestUrl()
        {
            $url = $_SERVER["REQUEST_URI"];
            if (valid(BaseConfig::SITE_FOLDER))
            {
                /* If the Site is within a subfolder, remove it from the URL arguments */
                $folder = rtrim(BaseConfig::SITE_FOLDER, '/') . '/';
                $url = str_replace($folder, "", $url);
            }
            return rtrim(ltrim($url, '/'), "/");
        }

        /**
         * @return The full URL of the page which the user is on
         */
        public static function fullRequestUrl()
        {
            return SystemConfig::baseUrl() . self::requestUrl();
        }

        /**
         * Gets the URL query
         * 
         * @return String - The URL Query
         */
        public static function getUrlQ()
        {
            $url = $_GET['urlq'];
            $curl = rtrim(ltrim($url, "/"), "/");

            if (!isset($curl) || "" == $curl)
            {
                return BaseConfig::HOME_URL;
            }

            return $curl;
        }

        /**
         * @return An array of arguments within the URL currently being viewed
         */
        public static function urlArgs($index = null)
        {
            $url = self::getUrlQ();
            $eurl = explode('/', $url);
            return ($index) ? $eurl[$index] : $eurl;
        }

        /**
         * Finds the different database Routes that handles the current URL
         * 
         * The different routes are computed based on the current url, where the '%' is the wildcard character.
         * 
         * Example: Current URL: a/b/c
         * Possible Routes:     a/b/c
         *                      a/b/%
         *                      a/%/%
         * 
         * @param $url The URL for which to check
         * 
         * @return Array[Route] The different routes that handles this URL
         */
        public static function getRoutes($url = null)
        {
            if (!valid($url))
            {
                $url = self::getUrlQ();
            }

            /* Lets generate the possible route->urls that can be called for this path */
            $url_parts = explode("/", $url);
            $possible_urls = array($url);

            for ($i = 0; $i < count($url_parts); $i++)
            {
                $temp = "";
                for ($j = 0; $j < $i; $j++)
                {
                    $temp .= $url_parts[$j] . "/";
                }

                for ($k = $j; $k < count($url_parts); $k++)
                {
                    $temp .= "%/";
                }

                $possible_urls[] = rtrim(ltrim($temp, "/"), "/");
            }

            $db = Codeli::getInstance()->getDB();

            $temp2 = "'" . implode("', '", $possible_urls) . "'";
            $sql = "SELECT * FROM " . DatabaseTables::ROUTE . " WHERE url IN ($temp2) AND method='::method'";
            $args = array("::method" => JPath::requestMethod());
            $results = $db->query($sql, $args);

            $handlers = array();

            while ($res = $db->fetchObject($results))
            {
                $route = new Route();
                $route->importData($res);
                $handlers[] = $route;
            }

            return $handlers;
        }

        /**
         * Parses a set of menus and:
         *      -> removes those items the specified user don't have premission to access
         *      -> Append the Site Base URL to each of the menu items if they don't already contain the base url
         * 
         * @param $menu An array in the form $url => $title
         * @param $uid The user from whose POV to parse the menu, the currently logged in user is default
         * 
         * @return String - The parsed menu
         */
        public static function parseMenu($menu, $uid = null)
        {
            /* If no user was specified, parse the menu for the current user */
            $user = Codeli::getInstance()->getUser();

            $uid = $user->uid;
            foreach ($menu as $url => $menuItem)
            {
                /* Remove the site base URL from the front of the menu if it exists there */
                $url1 = str_replace(SystemConfig::baseUrl(), "", $url);
                $url = ltrim(rtrim($url1));

                /* Remove this URL from the menu */
                unset($menu[$url]);

                $handlers = JPath::getRoutes($url);
                foreach ($handlers as $handler)
                {
                    if (!isset($handler['permission']) || !valid($handler['permission']))
                    {
                        /* There is no permission for this handler, add the URL to the menu */
                        $url = self::absoluteUrl($url);
                        $menu[$url] = $menuItem;
                        break;
                    }
                    else if ($user->usesPermissionSystem() && $user->hasPermission($handler['permission']))
                    {
                        /* The user has the permission, add the URL to the menu */
                        $url = self::absoluteUrl($url);
                        $menu[$url] = $menuItem;
                        break;
                    }
                }
            }
            return $menu;
        }

        /**
         * A support function to call absoluteUrl
         */
        public static function fullUrl($url)
        {
            return self::absoluteUrl($url);
        }

        /**
         * Creates an absolute site URL given a relative URL
         * 
         * @param $url the relative URL
         * 
         * @return The full site URL for a given URL string 
         */
        public static function absoluteUrl($url)
        {
            /* Replace the Base URL if it's already in the string */
            $url2 = str_replace(SystemConfig::baseUrl(), "", $url);

            /* Remove excess slashes from the URL */
            $url_trimmed = rtrim(ltrim($url2, "/"), "/");

            return SystemConfig::baseUrl() . "?urlq=" . ltrim($url_trimmed, "/");
        }

        public static function requestMethod()
        {
            return filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
        }

    }
    