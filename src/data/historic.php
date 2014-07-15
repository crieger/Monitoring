<?php

/* 
 * Copyright (C) 2014 Christophe
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Historic
{
    var $item = null;
    var $historic = null;
    
    function __construct($item, $historic)
    {
        $this->item = $item;
        $this->historic = $historic;
    }
    
    function getRepresentativeArray()
    {
        $arr = array();
        
        // Firstly the begin of the problem is always generated by the system.
        $obj = array("timestamp" => $this->item["timestamp"], "what" => "Création", "who" => "Système");
        array_push($arr, $obj);
        
        // Now loop inside interventions.
        if (count($this->historic) > 0)
        {
            for ($i=0; $i < count($this->historic); $i++)
            {
                // Start of intervention
                $obj = array("timestamp" => $this->historic[$i]["start_timestamp"], "what" => "Prise en charge", "who" => $this->historic[$i]["username"]);
                array_push($arr, $obj);

                // End of intervention if exists
                if (isset($this->historic[$i]["end_timestamp"]))
                {
                    // It is also the last event ?
                    if ($this->item["last_interv"] == $this->historic[$i]["end_timestamp"] && $this->item["resolved"] == 1)
                    {
                        $obj = array("timestamp" => $this->historic[$i]["end_timestamp"], "what" => "Résolu", "who" => $this->historic[$i]["username"]);
                        array_push($arr, $obj);
                    }
                    else
                    {
                        $obj = array("timestamp" => $this->historic[$i]["end_timestamp"], "what" => "Echec résolution", "who" => $this->historic[$i]["username"]);
                        array_push($arr, $obj);
                    }
                }
                else
                {
                    if ($this->item["resolved"] == 1)
                    {
                        $obj = array("timestamp" => $this->item["last_interv"], "what" => "Résolu", "who" => "Système");
                        array_push($arr, $obj);
                    }
                }
            }
        }
        else
        {            
            // Is the problem resolved ?
            if ($this->item["resolved"] == 1)
            {
                $obj = array("timestamp" => $this->item["last_interv"], "what" => "Résolu", "who" => "Système");
                array_push($arr, $obj);
            }
        }
        
        return $arr;
    }
    
    function getTimelineArray()
    {
        $arr = array();
        
        $now = date( 'Y-m-d H:i:s', time() );
        
        // Now loop inside interventions.
        if (count($this->historic) > 0)
        {
            // Waiting until first intervention
            $obj = array("start" => $this->item["timestamp"], "end" => $this->historic[0]["start_timestamp"], "label" => "En attente");
            array_push($arr, $obj);
            
            for ($i=0; $i <= count($this->historic) - 1; $i++)
            {
                if (isset($this->historic[$i]["end_timestamp"]))
                {
                    $obj = array("start" => $this->historic[$i]["start_timestamp"], "end" => $this->historic[$i]["end_timestamp"], "label" => $this->historic[$i]["username"]);
                    array_push($arr, $obj);
                    
                    if (isset($this->historic[$i+1]))
                    {
                        $obj = array("start" => $this->historic[$i]["end_timestamp"], "end" => $this->historic[$i+1]["start_timestamp"], "label" => "En attente");
                        array_push($arr, $obj);
                    }
                }
                else
                {
                    $obj = array("start" => $this->historic[$i]["start_timestamp"], "end" => $now, "label" => $this->historic[$i]["username"]);
                    array_push($arr, $obj);
                }
            }
            
            $lastIndex = count($this->historic) - 1;
            if ($this->item["resolved"] == 0 && isset($this->historic[$lastIndex]["end_timestamp"]))
            {
                $obj = array("start" => $this->item["last_interv"], "end" => $now, "label" => "En attente");
                array_push($arr, $obj);
            }
        }
        else
        {
            // Is the problem resolved ?
            if ($this->item["resolved"] == 1)
            {
                $obj = array("start" => $this->item["timestamp"], "end" => $this->item["last_interv"], "label" => "En attente");
                array_push($arr, $obj);
            }
            else
            {
                $obj = array("start" => $this->item["timestamp"], "end" => time(), "label" => "En attente");
                array_push($arr, $obj);
            }
        }
        
        return $arr;
    }
    
    function getLastActionUsername()
    {
        
    }
}