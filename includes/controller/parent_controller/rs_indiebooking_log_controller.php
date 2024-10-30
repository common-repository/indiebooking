<?php
/*
 * Indiebooking - the Booking Software for your Homepage!
 * Copyright (C) 2016  ReWa Soft GmbH
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
 */
?>
<?php if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}
// if ( ! class_exists( 'RS_Indiebooking_Log_Controller' ) ) :
/**
 * @author schmitt
 *
 */

class RS_Indiebooking_Log_Controller
{
    const RS_IB_LOG_TYPE_ERROR = "error";
    const RS_IB_LOG_TYPE_STAT  = "statistik";
    const RS_IB_LOG_TYPE_INFO  = "info";
    
    private static function getlogdir() {
//         $upload_dir     = wp_upload_dir();
//         $logdir         = $upload_dir['basedir'].DIRECTORY_SEPARATOR.'indiebooking_uploads';
        $logdir         = cRS_Indiebooking::file_upload_path();
        $logdir         = $logdir.DIRECTORY_SEPARATOR."iblog";
        return $logdir;
    }
    
    public static function getLastLogFile() {
        $logDir         = self::getlogdir();
        $files          = scandir($logDir, SCANDIR_SORT_DESCENDING);
        $newest_file    = $logDir.DIRECTORY_SEPARATOR.$files[0];
        return $newest_file;
    }
    
    public static function getLogFile($filename) {
        $logDir         = self::getlogdir();
        $file           = $logDir.DIRECTORY_SEPARATOR.$filename;
        return $file;
    }
    
    public static function getLastXLogFiles($countOfFiles = 1) {
        $filesArray     = array();
        $logDir         = self::getlogdir();
        if (file_exists($logDir)) {
            $files          = scandir($logDir, SCANDIR_SORT_DESCENDING);
            for ($i = 0; $i < $countOfFiles; $i++) {
                if (key_exists($i, $files)) {
                    if ($files[$i] !== "." && $files[$i] !== "..") {
                        array_push($filesArray, $files[$i]);
                    }
                }
            }
        }
        return $filesArray;
    }
    
    /* @var $errorLogTbl RS_IB_Table_ErrorLog */
    public static function write_error_db_log( $log, $extraId = 0, $extraTxt = "", $line = 0, $class = "", $typ = "default" ) {
    	global $RSBP_DATABASE;
    	
    	if (is_null($extraId)) {
    		$extraId 	= 0;
    	}
    	if (is_null($extraTxt)) {
    		$extraTxt 	= "";
    	}
    	$myLog         = "";
    	$logdir        = self::getlogdir();
    	$pluginurl     = cRS_Indiebooking::plugin_url();
    	$timezone      = get_option('timezone_string');
    	$now           = new DateTime();
    	if (!empty($timezone)) {
    	    $dtz       = new DateTimeZone($timezone);
    	    $now->setTimezone($dtz);
    	}
    	$dateString    = $now->format('Y_m_d');
    	$dateString2   = $now->format('Y-m-d H:i:s');
    	
    	$pagename      = get_bloginfo("name");
    	$pagename      = str_replace(" ", "", $pagename);
    	$pagename      = str_replace("/", "", $pagename);
    	$pagename      = str_replace(":", "", $pagename);
    	$filepath      = $logdir.DIRECTORY_SEPARATOR."log_".$pagename.$dateString.".json";
    	if ( is_array( $log ) || is_object( $log ) ) {
    		$myLog  = ( print_r( $log, true ) );
    	} else {
    		$myLog  = ( $log );
    	}
    	if ( true === WP_DEBUG ) {
    		if ($line != "" && $class != "") {
    			error_log( "[".$line." " . $class . "] ". $myLog );
    		} else {
    			error_log( $myLog );
    		}
    	}
    	if ($typ == self::RS_IB_LOG_TYPE_ERROR) {
    		//Logt den Fehler in die Datenbank
    		$errorLogTbl            = $RSBP_DATABASE->getTable(RS_IB_Model_ErrorLog::RS_TABLE);
    		$errorLogObj			= new RS_IB_Model_ErrorLog();
    		$errorLogObj->setClass($class);
    		$errorLogObj->setLine($line);
    		$errorLogObj->setType($typ);
    		$errorLogObj->setText($log);
    		$errorLogObj->setExtraId($extraId);
    		$errorLogObj->setExtraText($extraTxt);
    		$errorLogObj->setDate($dateString2);
    		$errorLogTbl->saveErrorLog($errorLogObj);
    	}
    	if (wp_mkdir_p( $logdir )) {
//     		$allowStatistics        = get_option('rs_indiebooking_settings_allow_statistics_kz');
// && $allowStatistics == "on"
    		if ($typ !== "default") {
    			if (file_exists($filepath)) {
    				$filecontent    = file_get_contents($filepath);
    				$jsoncontent    = json_decode($filecontent, true);
    			} else {
    				$jsoncontent = null;
    			}
    			$fp             = fopen($filepath, 'w');
    			$jLog  = array(
    					'type'  => $typ,
    					'msg'   => $log,
    					'line'  => $line,
    					'class' => $class,
    					//                     'url'   => $pluginurl,
    					'date'  => $dateString2,
    			);
    			if (!is_null($jsoncontent) && array_key_exists($typ, $jsoncontent)) {
    				array_push($jsoncontent[$typ], $jLog);
    			} else {
    				$jsoncontent[$typ][] = $jLog;
    			}
    			fwrite($fp, json_encode($jsoncontent, JSON_FORCE_OBJECT));
    			fclose($fp);
    		}
    	} else {
    		error_log( "could not create log dir" );
    	}
    }
    
    /* @var $errorLogTbl RS_IB_Table_ErrorLog */
    public static function write_log ( $log, $line = 0, $class = "", $typ = "default" )  {
    	self::write_error_db_log($log, 0, "", $line, $class, $typ);
    }
    
    public static function write_xml_file_to_log($xmlstring, $logName = "") {
    	$today 				= new DateTime("now");
    	$timestamp 			= $today->getTimestamp();
//     	$logdir     		= cRS_Indiebooking::plugin_path()."/iblog";
		$logdir				= self::getlogdir();
		if ($logName == "") {
    		$geht 			= file_put_contents($logdir."/ibToBcom_".$timestamp.".xml", $xmlstring);
		} else {
			$geht 			= file_put_contents($logdir."/".$logName."_".$timestamp.".xml", $xmlstring);
		}
    }
    
    public static function send_log_to_server() {
        $allowStatistics        = get_option('rs_indiebooking_settings_allow_statistics_kz');
        write_log("AllowStatistik: ".$allowStatistics);
        if (false && $allowStatistics == "on") {
            try {
                $logdir                 = self::getlogdir();
                $target_url             = "http://indiebooking.de/indiebooking_log/indiebooking_log.php";
                if ($handle = opendir($logdir)) {
                    $data               = array('jsondata' => array());
                    while (false !== ($entry = readdir($handle))) {
                        if($entry != "." && $entry != ".." && $entry != 'old') {
                            $filename   = $logdir."/".$entry;
                            RS_Indiebooking_Log_Controller::write_log(
                                "Log Datei gefunden: ".$entry,
                                __LINE__,
                                __CLASS__,
                                RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO
                            );
                            if (function_exists('curl_file_create')) { // php 5.6+
                                $curlfile = curl_file_create($filename);
                            } else {
                                $curlfile = '@' . realpath($filename);
                            }
                            $ch         = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $target_url);
                            curl_setopt($ch, CURLOPT_HEADER, false);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
//                             if (!function_exists('curl_file_create')) { // php 5.6+
//                                 curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
//                             }
                            curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                                'indiebooking_log_file' => $curlfile,
                            ));
                            $result     = curl_exec($ch);
                            $curlinfo   = curl_getinfo($ch);
                            $logmsg     = "";
                            if ($result === false || $curlinfo['http_code'] != 200) {
                                $logmsg = "No cURL data returned for $url [". $curlinfo['http_code']. "]";
                                if (curl_error($ch))
                                    $logmsg .= "\n". curl_error($ch);
                            }
                            curl_close($ch);
                            if ( true === WP_DEBUG ) {
                                if ($result == "file successfully uploaded") {
                                    RS_Indiebooking_Log_Controller::write_log(
                                        "Upload result ".$result,
                                        __LINE__,
                                        __CLASS__,
                                        RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO
                                    );
                                    if (wp_mkdir_p( $logdir."/old/" )) {
                                        $renameresult = rename($filename, $logdir."/old/".$entry);
                                        if (!$renameresult) {
                                            RS_Indiebooking_Log_Controller::write_log(
                                                "Datei konnte nicht in den Old Ordner verschoben werden",
                                                __LINE__,
                                                __CLASS__,
                                                RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
                                            );
                                        }
                                    } else {
                                        RS_Indiebooking_Log_Controller::write_log(
                                            "OLD - Ordner konnte nicht angelegt werden",
                                            __LINE__,
                                            __CLASS__,
                                            RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
                                        );
                                    }
                                } else {
                                    RS_Indiebooking_Log_Controller::write_log(
                                        "Upload result ".$logmsg,
                                        __LINE__,
                                        __CLASS__,
                                        RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
                                    );
                                }
                            } elseif ($result == "file successfully uploaded") {
                                unlink($filename);
                                RS_Indiebooking_Log_Controller::write_log(
                                    "Upload result ".$result,
                                    __LINE__,
                                    __CLASS__,
                                    RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO
                                );
                            } else {
                                RS_Indiebooking_Log_Controller::write_log(
                                    "Upload result ".$logmsg,
                                    __LINE__,
                                    __CLASS__,
                                    RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
                                );
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                RS_Indiebooking_Log_Controller::write_log(
                    $e->getMessage(),
                    __LINE__,
                    __CLASS__,
                    RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
                );
            }
        }
    }
}
// endif;
add_action('rs_indiebooking_logging_cron_event', array('RS_Indiebooking_Log_Controller', 'send_log_to_server'));