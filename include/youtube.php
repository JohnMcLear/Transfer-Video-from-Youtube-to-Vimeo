<?php
/*******************************************************************************
 *                      Youtube Class
 *******************************************************************************
 *      Author:     Vikas Patial
 *      Email:      admin@ngcoders.com
 *      Website:    http://www.ngcoders.com
 *
 *      File:       youtube.php
 *      Version:    1.0.0
 *      Copyright:  (c) 2008 - Vikas Patial
 *                  You are free to use, distribute, and modify this software 
 *                  under the terms of the GNU General Public License.  See the
 *                  included license.txt file.
 *      
 *******************************************************************************
 *  VERION HISTORY:
 *
 *       v1.1.0 [30.03.2011] - Fix
 *       v1.0.0 [18.9.2008] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 *
 *      NOTE: See www.ngcoders.com for the most recent version of this script 
 *      and its usage.
 *
 *******************************************************************************
*/


class youtube {
    var $conn = false;
    var $username = "";
    var $password = "";
    var $error = false;
    function get($url)
    {
        $this->conn = new Curl('youtube');
        $html = $this->conn->get($url);

        if(strstr($html,'verify-age-thumb'))
        {
            $this->error = "Adult Video Detected";
            return false;
        }

        if(strstr($html,'das_captcha'))
        {
            $this->error = "Captcah Found please run on diffrent server";
            return false;
        }

        if(!preg_match('/fmt_url_map=(.*?)&/',$html,$match))
        {
            $this->error = "Error Locating Downlod URL's";
            return false;
        }


        $fmt_url =  urldecode($match[1]);


        if(preg_match('/^(.*?)\\\\u0026/',$fmt_url,$match))
        {
            $fmt_url = $match[1];
        }

        $urls = explode(',',$fmt_url);
        $foundArray = array();

        foreach($urls as $url)
        {
            $format = explode('|',$url,2);
            $foundArray[$format[0]] = $format[1];
        }


        $formats = array(
            '13'=>array('3gp','Low Quality'),
            '17'=>array('3gp','Medium Quality'),
            '36'=>array('3gp','High Quality'),
            '5'=>array('flv','Low Quality'),
            '6'=>array('flv','Low Quality'),
            '34'=>array('flv','High Quality (320p)'),
            '35'=>array('flv','High Quality (480p)'),
            '18'=>array('mp4','High Quality (480p)'),
            '22'=>array('mp4','High Quality (720p)'),
            '37'=>array('mp4','High Quality (1080p)'),
        );

        foreach ($formats as $format => $meta) {
            if (isset($foundArray[$format])) {
                $videos[] = array('ext' => $meta[0], 'type' => $meta[1], 'url' => $foundArray[$format]);
            }
        }

        return $videos;
    }
}
