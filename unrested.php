<?php


class unrested {
	private $registered_functions = array();

	public function register($method, $identifier, $function){
		if(!isset($this->registered_functions[$method])){
			$this->registered_functions[$method] = array();
		}

		$identifier_interim = str_replace('/', '\/', $identifier);
		$identifier_interim = preg_replace('/{string:(?<variable_name>[^}]+)}/mi','(?<$1>[^\/?]*)',$identifier_interim);
		$identifier_interim = preg_replace('/{integer:(?<variable_name>[^}]+)}/mi', '(?<$1>\d+)', $identifier_interim);
		
		$identifier_regex = '/\/api\/v(?<_api_version>\d+)' . $identifier_interim . '(\?(?<_query_param>.*))?$/mi';


		$this_function = array(
			'identifier_string' => $identifier,
			'identifier_regex'  => $identifier_regex,
			'function' 	    => $function,
		);

		$this->registered_functions[$method][] = $this_function;

	}

	public function run(){
		if(!isset($this->registered_functions[$_SERVER['REQUEST_METHOD']])){
			http_response_code(501);
			exit;
		}

		foreach($this->registered_functions[$_SERVER['REQUEST_METHOD']] as $function){
			$matches = array();
			if(preg_match($function['identifier_regex'], $_SERVER['REQUEST_URI'], $matches)){

				$http_body = file_get_contents('php://input');
				$json = json_decode($http_body);
				if(!is_array($json)){
					$json = array();
				}

				$input = array_merge($_GET, $_POST, $json, $matches);

				$result = $function['function']($input);
				http_response_code($result[0]);
				if(isset($_GET['pretty_print'])){
					echo json_encode($result[1], JSON_PRETTY_PRINT);
				}else{
					echo json_encode($result[1]);
				}
				exit;
			}
		}

		http_response_code(501);
	}
}

