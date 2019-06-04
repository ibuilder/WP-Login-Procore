<?php

class Procore_API{

    private $client_id;
    private $client_secret;

    public function __construct() {
        $general_settings = (array) get_option( 'procore_api_settings' );
        $this->client_id = $general_settings["client_id"];
        $this->client_secret = $general_settings["client_secret"];
    }

    public function refresh_token($token){
        $ch = curl_init(); 
        // set url 
        curl_setopt($ch, CURLOPT_URL, "https://app.procore.com/oauth/token?grant_type=refresh_token&client_id=".$this->client_id."&client_secret=".$this->client_secret."&refresh_token=".$token); 

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1);

        // $output contains the output string 
        $output = curl_exec($ch); 
        // close curl resource to free up system resources 
        curl_close($ch);
        
        $json = json_decode($output);
        if($json->access_token){
            update_option('procore-token', $json->refresh_token);
            return true;
        }
        return false;
    }

    public function get_companies($store = false){
        if($this->get_access_token()){
                $ch = curl_init(); 

            // set url 
            curl_setopt($ch, CURLOPT_URL, "https://app.procore.com/vapid/companies"); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer '.$this->get_access_token()
            ));

            //return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            //curl_setopt($ch, CURLOPT_POST, 1);

            // $output contains the output string 
            $output = curl_exec($ch); 
            $json = json_decode($output, true);
            if(count($json) > 0 && isset($json[0]['id'])){
                if($store){
                    update_option('procore_companies', $output);
                }
                return $json;
            }
            // close curl resource to free up system resources 
            curl_close($ch);  
        }
        return false;
    }

    public function get_projects($company_id, $store = false){
        if($this->get_access_token()){
            $ch = curl_init(); 

            // set url 
            curl_setopt($ch, CURLOPT_URL, "https://app.procore.com/vapid/projects?company_id=".$company_id); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer '.$this->get_access_token()
            ));

            //return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            //curl_setopt($ch, CURLOPT_POST, 1);

            // $output contains the output string 
            $output = curl_exec($ch);

            $json = json_decode($output, true);
            if(count($json) > 0 && isset($json[0]['id'])){
                if($store){
                    update_option('procore_projects', $output);
                }
                return $json;
            }
            // close curl resource to free up system resources 
            curl_close($ch);  
        }
        return false;
    }

    public function get_project($project_id, $store = false){
        if($this->get_access_token()){
            $companies = $this->get_companies();
            if(!$companies)
                return false;
            $comp = array();
            foreach($companies as $company){
                $projects = $this->get_projects($company['id']);
                if($projects){
                    foreach($projects as $project){
                        if($project['id'] == $project_id){
                            $comp = $company;
                            break 2;
                        }
                    }
                }
            }

            if(!$comp)
                return false;
            $ch = curl_init(); 

            // set url 
            curl_setopt($ch, CURLOPT_URL, "https://app.procore.com/vapid/projects/".$project_id."/?company_id=".$comp['id']); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer '.$this->get_access_token()
            ));

            //return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            //curl_setopt($ch, CURLOPT_POST, 1);

            // $output contains the output string 
            $output = curl_exec($ch);

            $json = json_decode($output, true);
            if(isset($json['id'])){
                if($store){
                    update_option('procore_projects', $output);
                }
                return $json;
            }
            // close curl resource to free up system resources 
            curl_close($ch);  
        }
        return false;
    }
    
    public function get_logs($log, $project_id, $date = '', $store = false){
        if($this->get_access_token()){
            $ch = curl_init(); 
            $query_date = '';
            if($date){
                if($log == "weather_logs"){
                    $query_date = '?start_date='.$date.'&end_date='.$date;
                }
                else{
                    $query_date = '?log_date='.$date;
                }
            }

            // set url 
            curl_setopt($ch, CURLOPT_URL, "https://app.procore.com/vapid/projects/".$project_id."/".$log.$query_date); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer '. $this->get_access_token()
            ));

            //return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            //curl_setopt($ch, CURLOPT_POST, 1);

            // $output contains the output string 
            $output = curl_exec($ch);

            $json = json_decode($output, true);
            if(count($json) > 0 && isset($json[0]['id'])){
                if($store){
                    update_option('procore_'.$log, $output);
                }
                return $json;
            }
            // close curl resource to free up system resources 
            curl_close($ch);  
        }
        return false;
    }

    public function get_access_token($code = '') {
        if ($code == '') {
            $access_token = get_option( 'procore-token');
            if($access_token){
                return $access_token;
            }
            return false;
        }
        global $wp;

        $redirect_uri = site_url(add_query_arg(array(),$wp->request));
        $ch = curl_init(); 
        // set url 
        curl_setopt($ch, CURLOPT_URL, "https://app.procore.com/oauth/token?grant_type=authorization_code&client_id=".$this->client_id."&client_secret=".$this->client_secret."&code=".$code."&redirect_uri=". $redirect_uri); 

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1);

        // $output contains the output string 
        $output = curl_exec($ch); 
        // close curl resource to free up system resources 
        curl_close($ch);
        
        $json = json_decode($output);
         if($json->access_token){
            update_option('procore-token', $json->access_token);
            return $json->access_token;
        }
        return false;
    }

    public function get_user_info() {
       
        if($this->get_access_token()){
            $ch = curl_init(); 
            // set url 
            curl_setopt($ch, CURLOPT_URL, "https://app.procore.com/vapid/me/"); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer '.$this->get_access_token()
            ));

            //return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            //curl_setopt($ch, CURLOPT_POST, 1);

            // $output contains the output string 
            $output = curl_exec($ch);

            $json = json_decode($output, true);
            if (isset($json['id'])) {
                update_option('procore-user-info', $json);
                return $json; 
            }
            curl_close($ch);
            return false;
        }
        return false;
    }

    
}