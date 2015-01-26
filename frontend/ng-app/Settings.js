/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


CodeliApp.factory('Settings', function ()
{
    var Settings = {};
    Settings.homeUrl = "Codeli";
    Settings.templatesPath = "frontend/templates/";

    return {
        homeUrl: Settings.homeUrl,
        getTemplate: function (templateFile)
        {
            return Settings.templatesPath + templateFile;
        }
    };
});