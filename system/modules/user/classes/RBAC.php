<?php

    /**
     * Description of RBAC
     *
     * @author Joshua
     */
    class RBAC
    {
        public static function getAllRoles()
        {
            $db = Codeli::getInstance()->getDB();

            $sql = "SELECT * FROM " . DatabaseTables::ROLE;
            $results = $db->query($sql);

            $data = array();
            while ($res = $db->fetchObject($results))
            {
                $ct = new Role();
                $ct->loadFromMap($res);
                $data[] = $ct;
            }

            return $data;
        }
    }
    