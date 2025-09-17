<?php
		class CFTP {

		var $server = "ftp.intellimarketing.com";
		var $user = "TheBrandFactory";
		var $pwd = "JaA@Zsk6";
//		var $port = 7506;
		var $conn_id = "";

		var $status = "closed";
		var $error = "";
		var $error_msg = "";

		var $basepath = "/";
		var $mode = FTP_BINARY;
		/** comment here */
		function __construct() {
			$this->conn_id = ftp_connect($this->server, $this->port);	
			if ($this->conn_id) {
				$this->status = "open";
				$this->error = 0;
			} else {
				$this->status = "closed";
				$this->error = -1;
			}

			if ($this->status == "open") {
				$login_result = ftp_login($this->conn_id, $this->user, $this->pwd);
				if ($login_result) {
					$this->status == "logged";
					$this->error = 0;
					ftp_pasv($this->conn_id, true);
				} else {
					$this->status == "open";
					$this->error = -2;
				}  
			}
		}

		/** comment here */
		function upload($source, $target, $mode) {
			if ($this->status != "logged") {
				$this->__construct();
			}
			if ($this->error) Return $this->error;
			else {
				$target  = $this->basepath . $target;
				if (!$mode) $mode = $this->mode;
				$upload = ftp_put($this->conn_id, $target, $source, $mode);
				if ($upload) Return 1; else {
					$this->error = -3;
					Return $this->error;
				}
			}
		}

		/** comment here */
		function makedir($d) {
						if ($this->status != "logged") {
				$this->__construct();
			}
			if ($this->error) Return $this->error;
			$d  = $this->basepath . $d;
			ftp_mkdir($this->conn_id, $d);
			Return 1;
		}

		/** comment here */
		function close() {
			ftp_close($this->conn_id);
		}

		
		
		}

?>