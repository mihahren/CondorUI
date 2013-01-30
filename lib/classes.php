<?php
include_once "functions.php";

/* RAZRED ZA USER MANAGEMENT */

class UserManager
{
	// spremenljivke
	protected $dblink = "";
	private $userid = "";
	private $username = "";
	private $password = "";
	private $email = "";
	private $isadmin = "";
	private $registertime = "";
	private $activetime = "";
	private $dayspassed = "";
	private $daysleft = "";

	public $accesscontrol = "login";

	// constructor vzpostavi povezavo z bazo in zacne session
	public function __construct($db_host="127.0.0.1", $db_user="root", $db_pass="", $db="condor_users")
	{
		$this->dblink = mysql_connect($db_host, $db_user, $db_pass);

		if (!$this->dblink)
		{
			echo "Database connection failed.";
			exit;
		}
	
		$db_handle = mysql_select_db($db, $this->dblink);
	
		if(!$db_handle)
		{
			echo "Error selecting database.";
			exit;
		}

		session_start();
    }

	// destructor unici session in zapre povezavo z bazo
	public function __destruct()
	{
		//mysql_close($this->dblink);
    }
	
	// preveri obstoj kombinacije uporabnik + password in shrani njegove podatke v objektu
	public function selectUser($user_name, $password)
	{
		$query = "SELECT * FROM users WHERE username = '".$user_name."' AND password = PASSWORD('".$password."')";
		$result = mysql_query($query, $this->dblink);

		if (mysql_num_rows($result) == 0)
		{
			$this->accesscontrol = "no_access";
			$this->userid = "";
			$this->username = "";
			$this->password = "";
			$this->email = "";
			
			return false;
		}
		else
		{
			if (mysql_result($result,0,"isadmin") == 1)
			{
				$this->accesscontrol = "admin";
			}
			else
			{
				$this->accesscontrol = "access";
			}

			$this->userid = mysql_result($result,0,"userid");
			$this->username = $user_name;
			$this->password = $password;
			$this->email = mysql_result($result,0,"email");
			$this->isadmin = mysql_result($result,0,"isadmin");
			$this->registertime = mysql_result($result,0,"registertime");
			$this->activetime = mysql_result($result,0,"activetime");
			$this->dayspassed = time() - $this->registertime;

			if ($this->activetime == 0)
			{
				$this->daysleft = "inf";
			}
			else
			{
				$this->daysleft = $this->activetime - $this->dayspassed;
			}

			return true;
		}
	}

	// preveri obstoj uporabniskega imena
	public function checkUsernameExistance($username)
	{
		$query = "SELECT * FROM users WHERE username = '".$username."'";
		$result = mysql_query($query, $this->dblink);

		if (mysql_num_rows($result) >= 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	// preveri obstoj emaila
	public function checkEmailExistance($email)
	{
		$query = "SELECT * FROM users WHERE email = '".$email."'";
		$result = mysql_query($query, $this->dblink);

		if (mysql_num_rows($result) >= 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	// preveri vse kontrole za username
	public function usernameControl($username)
	{
		$result_array = array();

		if ($username == "") // preveri,ce je polje zapolnjeno
		{
			$result_array[0] = "Polje uporabniško ime je prazno.";
		}
		if ($this->checkUsernameExistance($username)) // preveri obstoj username
		{
			$result_array[1] = "Uporabniško ime že obstaja.";
		}
		if (3 > strlen($username) && $username != "") // preveri dolzino username
		{
			$result_array[2] = "Prekratko uporabniško ime.";
		}

		return $result_array;
	}

	// preveri vse kontrole za password
	public function passwordControl($password)
	{
		$result_array = array();

		if ($password == "") // preveri,ce je polje zapolnjeno
		{
			$result_array[0] = "Polje geslo je prazno.";
		}
		if (5 > strlen($password) && $password != "") // preveri dolzino password
		{
			$result_array[1] = "Prekratko geslo.";
		}

		return $result_array;
	}

	// preveri vse kontrole za email
	public function emailControl($email)
	{
		$result_array = array();

		if ($email == "") // preveri,ce je polje zapolnjeno
		{
			$result_array[0] = "Polje email je prazno.";
		}
		if ($this->checkEmailExistance($email)) // preveri obstoj email
		{
			$result_array[1] = "Email že obstaja.";
		}
		if (3 > strlen($email) && $email != "") // preveri dolzino email
		{
			$result_array[2] = "Prekratka elektronska pošta.";
		}

		return $result_array;
	}

	// vnese novega uporabnika v bazo
	public function inputNewUser($user_name, $password, $email, $is_admin, $register_time, $active_time)
	{
		$result_array = array();

		// pogoji za registracijo
		$result_array[0] = $this->usernameControl($user_name);
		$result_array[1] = $this->passwordControl($password);
		$result_array[2] = $this->emailControl($email);
		
		// registracija
		if (empty($result_array[0]) && empty($result_array[1]) && empty($result_array[2]))
		{
			$query = "INSERT INTO users VALUES (NULL,'$user_name',PASSWORD('$password'),'$email','$is_admin','$register_time','$active_time')";
			$result = mysql_query($query, $this->dblink);
			if ($result)
			{
				$result_array[0] = "Uspešno registrirano.";
			}
			else
			{
				$result_array[1] = "Prišlo je do napake pri vnosu.";
			}
		}

		return $result_array;
	}

	// spremeni uporabnisko ime, password, email - za uporabnika
	public function editUser($user_name, $password, $new_user_name, $new_password, $new_email)
	{
		if($this->selectUser($user_name, $password)) // preveri prvotni username in password
		{
			$result_array = array();
			
			// pogoji za urejanje
			$result_array[0] = $this->usernameControl($new_user_name);
			$result_array[1] = $this->passwordControl($new_password);
			$result_array[2] = $this->emailControl($new_email);

			// username urejanje
			if (empty($result_array[0]))
			{
				$query = "UPDATE users SET username = '".$new_user_name."' WHERE userid = ".$this->userid;
				$result = mysql_query($query, $this->dblink);
				$user_name = $new_user_name;
				$this->loginUser($user_name, $password);
				$result_array[0] = "Uporabniško ime spremenjeno.";
			}

			// password urejanje
			if (empty($result_array[1]))
			{
				$query = "UPDATE users SET password = PASSWORD('".$new_password."') WHERE userid = ".$this->userid;
				$result = mysql_query($query, $this->dblink);
				$password = $new_password;
				$this->loginUser($user_name, $password);
				$result_array[1] = "Geslo spremenjeno.";
			}

			// email urejanje
			if (empty($result_array[2]))
			{
				$query = "UPDATE users SET email = '".$new_email."' WHERE userid = ".$this->userid;
				$result = mysql_query($query, $this->dblink);
				$result_array[2] = "Elektronska pošta spremenjena.";
			}

		}
		else
		{
			$result_array[0] = "Napačno uporabniško ime ali geslo.";
		}

		return $result_array;
	}

	// spremeni uporabnisko ime, password, email - za admina
	public function editUserAdmin($user_name, $new_user_name, $new_password, $new_email)
	{
		$result_array = array();

		// pogoji za urejanje
		$result_array[0] = $this->usernameControl($new_user_name);
		$result_array[1] = $this->passwordControl($new_password);
		$result_array[2] = $this->emailControl($new_email);

		// username checking
		if (empty($result_array[0]))
		{
			$query = "UPDATE users SET username = '".$new_user_name."' WHERE username = '".$user_name."'";
			$result = mysql_query($query, $this->dblink);
			$user_name = $new_user_name;
			$result_array[0] = "Uporabniško ime spremenjeno.";
		}

		// password checking
		if (empty($result_array[1]))
		{
			$query = "UPDATE users SET password = PASSWORD('".$new_password."') WHERE username = '".$user_name."'";
			$result = mysql_query($query, $this->dblink);
			$result_array[1] = "Gelso spremenjeno.";
		}

		// email checking
		if (empty($result_array[2])) // preveri novi email
		{
			$query = "UPDATE users SET email = '".$new_email."' WHERE username = '".$user_name."'";
			$result = mysql_query($query, $this->dblink);
			$result_array[2] = "Elektronska pošta spremenjena.";
		}

		return $result_array;
	}

	// izpisi query seznam v array
	public function getUserArray($query)
	{
		$array = array();
		$iter = 0;
		$result = mysql_query($query, $this->dblink);

		while($row = mysql_fetch_array($result))
		{
			$array[$iter] = $row[0];
			$iter++;
		}

		return $array;
	}

	// preveri, koliko dni ima se oseba na razpolago
	public function daysLeft($user_name, $password)
	{
		if ($this->selectUser($user_name, $password))
		{
			if (($this->dayspassed <= $this->activetime) || ($this->activetime == 0))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	// ustvari session za uporabnika
	public function loginUser($user_name, $password)
	{
		if ($this->selectUser($user_name, $password))
		{
			if ($this->daysLeft($user_name, $password))
			{
				$_SESSION['access'] = $this->accesscontrol;
				$_SESSION['login_id'] = $this->userid;
				$_SESSION['username'] = $this->username;
				$_SESSION['password'] = $this->password;
				$_SESSION['email'] = $this->email;
				$_SESSION['daysleft'] = $this->daysleft;
				$_SESSION['isadmin'] = $this->isadmin;
				
				return true;
			}
			else
			{
				$_SESSION['access'] = "time_out";
				unset($_SESSION['login_id']);
				unset($_SESSION['username']);
				unset($_SESSION['password']);
				unset($_SESSION['email']);
				unset($_SESSION['daysleft']);
				
				return false;
			}
		}
		else
		{
			$_SESSION['access'] = "no_access";
			unset($_SESSION['login_id']);
			unset($_SESSION['username']);
			unset($_SESSION['password']);
			unset($_SESSION['email']);
			unset($_SESSION['daysleft']);
			
			return false;
		}
	}

	// izbrise session za uporabnika
	public function logoutUser()
	{
		$_SESSION = array();
		session_destroy();
	}

	// izbrise uporabnika
	public function deleteUser($username)
	{
		$result = mysql_query("SELECT userid FROM users WHERE username = '".$username."'", $this->dblink);
		$temp = mysql_fetch_row($result);
		
		$result = mysql_query("DELETE FROM stats WHERE userid = '".$temp[0]."'", $this->dblink);
		$result = mysql_query("DELETE FROM users WHERE userid = '".$temp[0]."'", $this->dblink);
		
		if ($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	// izpise vse uporabnike v tabeli
	public function displayUsers()
	{
		echo "ni dokončano";
	}
}

/* RAZRED ZA FILE MANAGEMENT */

class FileManager
{
	// spremenljivke
	private $root = "";
	private $localRoot = "";

	// constructor postavi root za brskanje file-ov in ustvari privzete mape za prijavljenega uporabnika, ce se ne obstajajo
	public function __construct($lroot = "")
	{
		$this->localRoot = $lroot;
		$this->root = $lroot.$_SESSION['login_id']."/";
		$this->makeDir($this->root, $out);
    }

	// destructor unici session in zapre povezavo z bazo
	public function __destruct()
	{
		$this->root = "";
    }

	// skenira directory
	public function scanDir($directory = "")
	{
		$dir = array();
		$iter = 0;
		$scanDir = scandir($this->root.$directory);

		for ($i=0; $i<count($scanDir); $i++)
		{
			if($scanDir[$i] != "." && $scanDir[$i] != "..")
			{
				$dir[$iter] = $scanDir[$i];
				$iter++;
			}
		}

		return $dir;
	}
	
	// skenira directory glede na local root
	public function localScanDir($directory = "")
	{
		$dir = array();
		$iter = 0;
		$scanDir = scandir($this->localRoot.$directory);

		for ($i=0; $i<count($scanDir); $i++)
		{
			if($scanDir[$i] != "." && $scanDir[$i] != "..")
			{
				$dir[$iter] = $scanDir[$i];
				$iter++;
			}
		}

		return $dir;
	}

	// ustvari directory
	private function makeDir($directory, &$output)
	{
		if (!preg_match("/[^a-z0-9_.-/\]/i", $directory))
		{
			if (!is_dir($Dir = $directory))
			{
				mkdir($Dir, 0775, true);
				return true;
			}
			return 0;
		}
		else
		{
			$output = "Ime mape lahko vsebuje samo številke, črke in znake _ . -";
			return false;
		}
	}

	// uploada file
	public function uploadFile($temp_file, $file, $upload_path, &$output)
	{
		if (!preg_match("/[^a-z0-9_.-]/i", $file))
		{
			if (file_exists($this->root.$upload_path.$file))
			{
				$output = "Datoteka ".$file." že obstaja!";
				return false;
			}
			else
			{
				move_uploaded_file($temp_file, $this->root.$upload_path.$file);
				$output = "Datoteka ".$file." uspešno prenesena. Nahaja se v root/".$upload_path.$file.".";
				return true;
			}
		}
		else
		{
			$output = "Ime datoteke lahko vsebuje samo številke, črke in znake _ . -";
			return false;
		}
	}
	
	// odzipa file
	public function unzipFile($file, &$output)
	{
		$zip = new ZipArchive;
		$res = $zip->open($this->root.$file);
		$dirFullName = pathinfo($file);

		if ($res === TRUE)
		{
			//popravek za pathinfo
			if ($dirFullName['dirname'] == ".")
				$dirFullName['dirname'] = "";
			else
				$dirFullName['dirname'] .= "/";
			
			//preveri, ce datoteka ze obstaja
			if (file_exists($this->root.$dirFullName['dirname'].$dirFullName['filename']))
			{
				$output = "Mapa root/".$dirFullName['dirname'].$dirFullName['filename']." že obstaja! Razpakiranje ni uspelo!";
				return false;
			}
			else
			{
				$zip->extractTo($this->root.$dirFullName['dirname'].$dirFullName['filename']);
				$zip->close();
				$output = "Datoteka ".$dirFullName['basename']." uspešno razpakirana v root/".$dirFullName['dirname'].$dirFullName['filename'].".";
				return true;
			}
		}
		else
		{
			$output = "Datoteka ".$dirFullName['basename']." neuspešno razpakirana.";
			return false;
		}
	}

	// zbrise file
	public function removeFile($file)
	{
		if (is_dir($this->root.$file))
		{
			$scanDir = $this->scanDir($file);
			foreach ($scanDir as $value)
			{
				$this->removeFile($file."/".$value);
			}
			
			rmdir($this->root.$file);
		}
		else
		{
			unlink($this->root.$file);
		}
	}
	
	// preveri owner status submit datoteke
	public function correctSubmitFile($submitFile, $username)
	{
		$dirPath = pathinfo($submitFile);
	
		$readFile = fopen($submitFile,"r");
		$writeFile = fopen($submitFile.".temp","w");
		
		fwrite($writeFile, "+Webuser=\"".$username."\"".PHP_EOL);
		
		while(!feof($readFile))
		{
			$line = fgets($readFile);
			
			if (strpos($line, "+Owner") !== false || strpos($line, "+Webuser") !== false)
			{
				$line = "";
			}
			
			fwrite($writeFile, $line);
		}
	
		fclose($readFile);
		fclose($writeFile);
		
		unlink($submitFile);
		rename($submitFile.".temp", $submitFile);
	}

	// submitaj file v condor
	public function submitFile($files, $user_name, &$output)
	{
		if (is_array($files))
		{
			foreach ($files as $key => $value)
			{
				$file_name = pathinfo($this->root.$value);
				
				if(!file_exists($this->root.$value))
				{
					$output[$key] = "Datoteka ".$file_name['basename']." več ne obstaja!";
				}
				elseif(preg_match("/[^a-z0-9_.-]/i", $file_name['basename']))
				{
					$output[$key] = "Ime submitane datoteke lahko vsebuje samo številke, črke in znake _ . -";
				}
				else
				{
					$this->correctSubmitFile($this->root.$value, $user_name);
					condor_generic('cd '.$file_name['dirname'].' && condor_submit '.$file_name['basename'], $submitOut);
					$output[$key] = $submitOut;
				}
			}
		}
		else
		{
			$file_name = pathinfo($this->root.$files);
			
			if(!file_exists($this->root.$files))
			{
				$output['submit'] = "Datoteka ".$file_name['basename']." več ne obstaja!";
			}
			elseif(preg_match("/[^a-z0-9_.-]/i", $file_name['basename']))
			{
				$output['submit'] = "Ime submitane datoteke lahko vsebuje samo številke, črke in znake _ . -";
			}
			else
			{
				$this->correctSubmitFile($this->root.$files, $user_name);
				condor_generic('cd '.$file_name['dirname'].' && condor_submit '.$file_name['basename'], $submitOut);
				$output['submit'] = $submitOut;
			}
		}
	}

	// izpise vse file v podanem direktoriju
	public function displayFolders($directory = "", $php_post_file, $result_div_id)
	{

		$scanDir = $this->scanDir($directory);
		$dirFullName = pathinfo($this->root.$directory);
		$dirName = $dirFullName['basename'];
		$directoryArray = splitString($directory, "/");
		$iter = "";
		
		// prva vrstica za navigacijo po folderjih
		echo "<ul class='breadcrumb'>
			<li><a class='mouse_hover' onclick=\"\$.ajax({
				url: '".$php_post_file."',
				type: 'POST',
				data: {directory: ''},
				success: function(result){\$('".$result_div_id."').html(result);}
			})\">root</a> <span class='divider'>/</span></li>";
			if ($directoryArray[0] != "")
			{
				foreach ($directoryArray as $key => $value)
				{
					$iter .= $value."/";
					echo "<li><a class='mouse_hover' onclick=\"\$.ajax({
						url: '".$php_post_file."',
						type: 'POST',
						data: {directory: '".$iter."/'},
						success: function(result){\$('".$result_div_id."').html(result);}
					})\">".$value."</a> <span class='divider'>/</span></li>";
				}
			}
		echo "</ul>";

		// tabela za file management
		echo "<table id='file_table' class='table table-condensed'><thead>
			<tr>
				<th class='span4'>Ime</th>
				<th class='span2'>Tip</th>
				<th class='span1'>Razpakiraj</th>
				<th class='span1' style='text-align: center;'>Predloži</th>
				<th class='span1' style='text-align: center;'>Odstrani</th>
			</tr></thead><tbody>";

			//izpise vse folderje
			for ($i=0; $i<count($scanDir); $i++)
			{
				$fullFileName = pathinfo($scanDir[$i]);

				if(is_dir($this->root.$directory.$scanDir[$i]))
				{
					echo "<tr>
						<td><i class='icon-folder-open'></i> <a class='mouse_hover' onclick=\"\$.ajax({
							url: '".$php_post_file."',
							type: 'POST',
							data: {directory: '".$directory.$scanDir[$i]."/'},
							success: function(result){\$('".$result_div_id."').html(result);}
						})\">".$scanDir[$i]."</a></td>
						<td><div>folder</div></td>
						<td></td>
						<td></td>
						<td style='text-align: center;'><a class='mouse_hover' onclick=\"\$.ajax({
							url: '".$php_post_file."',
							type: 'POST',
							data: {delete_file: '".$directory.$fullFileName['basename']."'},
							success: function(result){\$('".$result_div_id."').html(result);}
						})\"><i class='icon-trash'></i></a></td>
					</tr>";
				}
			}

			//izpise .condor in .submit file
			for ($i=0; $i<count($scanDir); $i++)
			{
				$fullFileName = pathinfo($scanDir[$i]);
				$fileNameArray = array("submit","sub","condor");
	
				if(in_array($fullFileName['extension'], $fileNameArray))
				{
					echo "<tr>
						<td><i class='icon-file'></i> <a href='/CondorUI".ltrim($this->root, ".").$directory.$fullFileName['basename']."'>".$fullFileName['basename']."</a></td>
						<td>".$fullFileName['extension']."</td>
						<td></td>
						<td style='text-align: center;'><a class='mouse_hover' onclick=\"\$.ajax({
							url: '".$php_post_file."',
							type: 'POST',
							data: {submit_file: '".$directory.$fullFileName['basename']."'},
							success: function(result){\$('".$result_div_id."').html(result);}
						})\"><i class='icon-upload'></i></a></td>
						<td style='text-align: center;'><a class='mouse_hover' onclick=\"\$.ajax({
							url: '".$php_post_file."',
							type: 'POST',
							data: {delete_file: '".$directory.$fullFileName['basename']."'},
							success: function(result){\$('".$result_div_id."').html(result);}
						})\"><i class='icon-trash'></i></a></td>
					</tr>";
				}
			}

			//izpise vse zip file
			for ($i=0; $i<count($scanDir); $i++)
			{
				$fullFileName = pathinfo($scanDir[$i]);
	
				if($fullFileName['extension'] == "zip")
				{
					echo "<tr>
						<td><i class='icon-file'></i> <a href='/CondorUI".ltrim($this->root, ".").$directory.$fullFileName['basename']."'>".$fullFileName['basename']."</a></td>
						<td>".$fullFileName['extension']."</td>
						<td style='text-align: center;'><a class='mouse_hover' onclick=\"\$.ajax({
							url: '".$php_post_file."',
							type: 'POST',
							data: {unzip_file: 'true', file_zip: '".$directory.$fullFileName['basename']."'},
							success: function(result){\$('".$result_div_id."').html(result);}
						})\"><i class='icon-upload'></i></a></td>
						<td></td>
						<td style='text-align: center;'><a class='mouse_hover' onclick=\"\$.ajax({
							url: '".$php_post_file."',
							type: 'POST',
							data: {delete_file: '".$directory.$fullFileName['basename']."'},
							success: function(result){\$('".$result_div_id."').html(result);}
						})\"><i class='icon-trash'></i></a></td>
					</tr>";
				}
			}

			//izpise vse ostale file
			for ($i=0; $i<count($scanDir); $i++)
			{
				$fullFileName = pathinfo($scanDir[$i]);
				$fileNameArray = array("submit","sub","condor","zip");

				if(!is_dir($this->root.$directory.$scanDir[$i]) && !in_array($fullFileName['extension'],$fileNameArray))
				{
					echo "<tr>
						<td><i class='icon-file'></i> <a href='/CondorUI".ltrim($this->root, ".").$directory.$fullFileName['basename']."'>".$fullFileName['basename']."</a></td>
						<td>".$fullFileName['extension']."</td>
						<td></td>
						<td></td>
						<td style='text-align: center;'><a class='mouse_hover' onclick=\"\$.ajax({
							url: '".$php_post_file."',
							type: 'POST',
							data: {delete_file: '".$directory.$fullFileName['basename']."'},
							success: function(result){\$('".$result_div_id."').html(result);}
						})\"><i class='icon-trash'></i></a></td>
					</tr>";
				}
			}
			
			if (count($scanDir) == 0)
			{
				echo "<tr><td colspan='5'>Mapa je prazna.</td></tr>";
			}
		
			// zadnja vrstica za zakljucek tabele
		echo "</tbody></table>";
	}
	
	//ustvari ida submit file
	public function createIdaSubmitFile($ida_acc_name, $ida_end_time, $ida_pga, $ida_per, $ida_xdamp, $username, &$output)
	{
		$this->makeDir($this->root."idacurves", $output[0]);
		$file = fopen($this->root."idacurves/ida.sub","w+");
	
			//string za vpisat v submit datoteko
			$string="+Webuser = \"".$username."\"
			universe = vanilla
			requirements = OpSys == \"LINUX\"
			should_transfer_files = YES
			when_to_transfer_output = ON_EXIT
			
			executable = ida.sh
			error = ida.err
			log = ida.log
			output = ida.out
			
			";
			
			for ($i=0;$i<count($ida_acc_name);$i++)
			{
				$string .= "arguments = ".$ida_end_time[$i]." ".$ida_pga[$i]." ".$ida_acc_name[$i]." ".$ida_per[$i]." ".$ida_xdamp[$i]." ".$ida_acc_name[$i]."_".$ida_per[$i]."_".$ida_xdamp[$i]."
				transfer_input_files = OpenSees_1_6_0_IKPIR, ida_template.tcl, SDOF_Spectra.tcl, ".$ida_acc_name[$i].".acc, ".$ida_acc_name[$i].".AEi
				queue
				
				";
			}
		
			$trimmedString=str_replace("\t","",$string);
			
			fwrite($file,$trimmedString);
	
		fclose($file);
		
		$output[1] = "Datoteka ida.sub uspesno ustvarjena. Nahaja se v idacurves mapi.";
	}
	
	//skopira potrebne ida file za submitat
	public function copyIdaFiles($ida_acc_name)
	{
		$this->makeDir($this->root."idacurves", $out);
		copy($this->localRoot.'ida_curves/generate-ida-sub.rb', $this->root.'idacurves/generate-ida-sub.rb');
		copy($this->localRoot.'ida_curves/ida.sh', $this->root.'idacurves/ida.sh');
		copy($this->localRoot.'ida_curves/ida_template.tcl', $this->root.'idacurves/ida_template.tcl');
		copy($this->localRoot.'ida_curves/OpenSees_1_6_0_IKPIR', $this->root.'idacurves/OpenSees_1_6_0_IKPIR');
		copy($this->localRoot.'ida_curves/SDOF_Spectra.tcl', $this->root.'idacurves/SDOF_Spectra.tcl');
		
		foreach ($ida_acc_name as $value)
		{
			copy($this->localRoot.'acceleration/'.$value.'.acc', $this->root.'idacurves/'.$value.'.acc');
			copy($this->localRoot.'acceleration/'.$value.'.AEi', $this->root.'idacurves/'.$value.'.AEi');
		}
	}
	
	// ustvari directory za uporabo izven tega razreda
	public function makeOutsideDir($directory, &$output)
	{
		if (!preg_match("/[^a-z0-9_.-\/]/i", $this->root.$directory))
		{
			if (!is_dir($this->root.$directory))
			{
				mkdir($this->root.$directory, 0775, true);
				$output = "Mapa uspešno ustvarjena.";
				return true;
			}
			
			$output = "Mapa že obstaja.";
			return 0;
		}
		else
		{
			$output = "Ime mape lahko vsebuje samo številke, črke in znake _ . - /";
			return false;
		}
	}
}

/* RAZRED ZA STATS TRACKING */

class StatsTracker extends UserManager
{
	// shrani statse v bazo podatkov
	public function storeStats($array)
	{
		$query = "INSERT INTO stats ";
		$combineValues = "(";
		$combineKeys = "(";

		foreach ($array as $key => $value)
		{
			$combineValues = $combineValues."'".$value."',";
			$combineKeys = $combineKeys.$key.",";
		}
		
		$combineValues = substr($combineValues, 0, -1).")";
		$combineKeys = substr($combineKeys, 0, -1).")";
		$query = $query.$combineKeys." VALUES ".$combineValues;
		$result = mysql_query($query, $this->dblink);
    }

	// vrne stevilo vrst na podlagi SQL ukaza
	public function getStatsRows($query)
	{
		$result = mysql_query($query, $this->dblink);
		$row = mysql_fetch_array($result);
		return $row[0];
    }

	// vrne array na podlagi SQL ukaza
	public function getStatsArray($query)
	{
		$array = array();
		$iter = 0;
		$result = mysql_query($query, $this->dblink);

		while($row = mysql_fetch_array($result))
		{
			$array[$iter] = $row;
			$iter++;
		}

		return $array;
    }
}

/* RAZRED ZA CONDOR MANAGEMENT */

class CondorManager
{
	// vhodne spremenljivke
	private $condorArray;
	private $elementNumber;
	private $currentPage;
	
	// drugi interni podatki
	private $arrayKeys;
	private $pageNumbers;
	private $minPage;
	private $maxPage;
	private $pagePrevStatus;
	private $pagePrev;
	private $pageNextStatus;
	private $pageNext;
	private $minDisplayPage;
	private $minDisplayCont;
	private $maxDisplayPage;
	private $maxDisplayCont;

	// constructor
	public function __construct($condor_Array, $element_Number = 15, $current_Page)
	{
		//shranjene spremenljivke
		$this->condorArray = $condor_Array;
		$this->elementNumber = $element_Number;
		$this->currentPage = $current_Page;
			
		//privzete vrednosti
		$this->pageNumbers = ceil(count($this->condorArray)/$this->elementNumber);
		
		if ($this->pageNumbers <= 0)
			$this->pageNumbers = 1;
		
		if (!isset($current_Page))
			$this->currentPage = 1;
		
		if ($this->currentPage >= $this->pageNumbers)
			$this->currentPage = $this->pageNumbers;				
		
		
		//spremenljivke za dolocitev min in max elementov trenutne strani
		if ($this->currentPage >= $this->pageNumbers)
			$this->minPage = $this->elementNumber * $this->pageNumbers - $this->elementNumber;
		elseif ($this->currentPage <=1 )
			$this->minPage = 0;
		else
			$this->minPage = $this->elementNumber * $this->currentPage - $this->elementNumber;
		
		if ($this->currentPage >= $this->pageNumbers)
			$this->maxPage = count($this->condorArray);
		elseif ($this->currentPage <= 1)
			$this->maxPage = $this->elementNumber;
		else
			$this->maxPage = $this->elementNumber * $this->currentPage;
		
		//spremenljivke za dolocitev min in max vidnih strani na navigaciji
		if (($this->currentPage - 2) > 0)
		{
			if (($this->currentPage + 2) > $this->pageNumbers)
				$this->minDisplayPage = $this->currentPage - 4 - ($this->currentPage - $this->pageNumbers);
			else
				$this->minDisplayPage = $this->currentPage - 2;
			
			if ($this->minDisplayPage < 1)
				$this->minDisplayPage = 1;
		}
		else
		{
			$this->minDisplayPage = 1;
		}
		
		if (($this->currentPage + 2) < $this->pageNumbers)
		{
			if (($this->currentPage - 2) < 1)
				$this->maxDisplayPage = $this->currentPage + 4 - ($this->currentPage - 1);
			else
				$this->maxDisplayPage = $this->currentPage + 2;
			
			if ($this->maxDisplayPage > $this->pageNumbers)
				$this->maxDisplayPage = $this->pageNumbers;
		}
		else
		{
			$this->maxDisplayPage = $this->pageNumbers;
		}
		
		//spremenljivke za dolocitev onemogocenja gumbov za naprej in nazaj
		if($this->currentPage <= 1)
		{
			$this->pagePrevStatus = "disabled";
			$this->pagePrev = $this->currentPage;
		}
		else
		{
			$this->pagePrevStatus = "";
			$this->pagePrev = $this->currentPage-1;
		}

		if($this->currentPage >= $this->pageNumbers)
		{
			$this->pageNextStatus = "disabled";
			$this->pageNext = $this->currentPage;
		}
		else
		{
			$this->pageNextStatus = "";
			$this->pageNext = $this->currentPage+1;
		}
	}
	
	//status condor status
	public function drawCondorStatusTable()
	{
		if(empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Condor gruča je prazna!</strong></div>";
		}
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th>Ime</th>
						<th>Op. sistem</th>
						<th>Arhitektura</th>
						<th>Stanje</th>
						<th>Aktivnost</th>
						<th>Obremenitev</th>
						<th>Spomin</th>";
					echo "</tr>
				</thead>
				<tbody>";
	
					// for zanka, ki gre skozi vse elelmente, ki bojo izpisani
					for ($i=$this->minPage;$i<$this->maxPage;$i++)
					{
						echo "<tr>";
							echo "<td>".$this->condorArray[$i]['Name']."</td>";
							echo "<td>".$this->condorArray[$i]['OpSys']."</td>";
							echo "<td>".$this->condorArray[$i]['Arch']."</td>";
							echo "<td>".$this->condorArray[$i]['State']."</td>";
							echo "<td>".$this->condorArray[$i]['Activity']."</td>";
							echo "<td>".round($this->condorArray[$i]['LoadAvg'],4)."</td>";
							echo "<td>".$this->condorArray[$i]['Memory']."</td>";
						echo "</tr>";
					}
						
				echo "</tbody>
			</table>";
		}	
	}
	
	//status condor status total
	public function drawCondorStatusTotalTable()
	{
		if(empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Condor gruča je prazna!</strong></div>";
		}
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th>Arhitektura</th>
						<th>Skupaj</th>
						<th>Zasedeni</th>
						<th>Prosti</th>";
					echo "</tr>
				</thead>
				<tbody>";
					
					// for zanka, ki gre skozi vse elelmente, ki bojo izpisani
					for ($i=$this->minPage;$i<$this->maxPage;$i++)
					{
						echo "<tr>";
							echo "<td>".$this->condorArray[$i]['Arch']."</td>";
							echo "<td>".$this->condorArray[$i]['Total']."</td>";
							echo "<td>".$this->condorArray[$i]['Claimed']."</td>";
							echo "<td>".$this->condorArray[$i]['Unclaimed']."</td>";
						echo "</tr>";
					}
						
				echo "</tbody>
			</table>";
		}	
	}
	
	//status condor computers
	public function drawCondorComputersTable()
	{
		if(empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Ni računalnikov!</strong></div>";
		}
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th>Naprava</th>
						<th>Stanje</th>";
					echo "</tr>
				</thead>
				<tbody>";
	
					// for zanka, ki gre skozi vse elelmente, ki bojo izpisani
					for ($i=$this->minPage;$i<$this->maxPage;$i++)
					{
						echo "<tr>";
							echo "<td>".$this->condorArray[$i]['Name']."</td>";
							if ($this->condorArray[$i]['Status'])
								echo "<td><img src='img/green_tick.png'/></td>";
							else
								echo "<td><img src='img/red_cross.png'/></td>";
						echo "</tr>";
					}
						
				echo "</tbody>
			</table>";
		}	
	}
	
	//status condor queue
	public function drawCondorStatusQTable()
	{
		if(empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Condor vrsta je prazna!</strong></div>";
		}
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th>Lastnik</th>
						<th>Predložene dat.</th>
						<th>Vsa opravila</th>";
					echo "</tr>
				</thead>
				<tbody>";
	
					// for zanka, ki gre skozi vse elelmente, ki bojo izpisani
					for ($i=$this->minPage;$i<$this->maxPage;$i++)
					{
						echo "<tr>";
							echo "<td>".$this->condorArray[$i]['Webuser']."</td>";
							echo "<td>".$this->condorArray[$i]['Total_cluster']."</td>";
							echo "<td>".$this->condorArray[$i]['Total']."</td>";
						echo "</tr>";
					}
						
				echo "</tbody>
			</table>";
		}
	}
	
	//control panel condor queue
	public function drawCondorQTable($ajax_Page, $output_ID, $user_name, $is_admin)
	{
		if(empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Condor vrsta je prazna!</strong></div>";
		}
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th>ID</th>
						<th>Lastnik</th>
						<th>Predloženo</th>
						<th>Čas teka</th>
						<th>Stanje</th>
						<th>Prioriteta</th>
						<th>Velikost</th>
						<th>Ime</th>
						<th>Zbriši</th>";
					echo "</tr>
				</thead>
				<tbody>";
	
					// for zanka, ki gre skozi vse elelmente, ki bojo izpisani
					for ($i=$this->minPage;$i<$this->maxPage;$i++)
					{
						echo "<tr>";
							echo "<td>".$this->condorArray[$i]['ClusterID'].".".$this->condorArray[$i]['ProcID']."</td>";
							echo "<td>".$this->condorArray[$i]['Webuser']."</td>";
							echo "<td>".date("d/m - H:i",$this->condorArray[$i]['JobStartDate'])."</td>";
							echo "<td>".date("H:i:s",$this->condorArray[$i]['CommittedTime']-60*60)."</td>";
							switch ($this->condorArray[$i]['JobStatus'])
							{
							case 0:
								echo "<td>Unexpanded</td>";
								break;
								
							case 1:
								echo "<td>Idle</td>";
								break;
								
							case 2:
								echo "<td>Running</td>";
								break;
								
							case 3:
								echo "<td>Removed</td>";
								break;
								
							case 4:
								echo "<td>Completed</td>";
								break;
								
							case 5:
								echo "<td>Held</td>";
								break;
								
							case 6:
								echo "<td>Submission error</td>";
								break;
							}
							echo "<td>".$this->condorArray[$i]['JobPrio']."</td>";
							echo "<td>".$this->condorArray[$i]['CoreSize']."</td>";
							echo "<td>".$this->condorArray[$i]['CMD']."</td>";
							
							if (($this->condorArray[$i]['Webuser'] == $user_name) || ($is_admin >= 1))
								echo "<td style='text-align: center;'><a class='mouse_hover'
								onclick=\"\$.ajax({
									url: '".$ajax_Page."',
									type: 'POST',
									data: {delete_submited_file: '".$this->condorArray[$i]['ClusterID'].".".$this->condorArray[$i]['ProcID']."'},
									success: function(result){\$('".$output_ID."').html(result);}
								})\"><i class='icon-trash'></i></a></td>";
							else
								echo "<td></td>";
						echo "</tr>";
					}
						
				echo "</tbody>
			</table>";
		}
	}
	
	//control panel condor queue samo za cluster
	public function drawCondorQClusterTable($ajax_Page, $output_ID, $user_name, $is_admin)
	{
		if(empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Condor vrsta je prazna!</strong></div>";
		}
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th>ID</th>
						<th>Lastnik</th>
						<th>Predloženo</th>
						<th>Čas teka</th>
						<th>Stanje</th>
						<th>Prioriteta</th>
						<th>Velikost</th>
						<th>Ime</th>
						<th>Zbriši</th>";
					echo "</tr>
				</thead>
				<tbody>";
	
					// for zanka, ki gre skozi vse elelmente, ki bojo izpisani
					for ($i=$this->minPage;$i<$this->maxPage;$i++)
					{
						echo "<tr>";
							echo "<td>".$this->condorArray[$i]['ClusterID']."</td>";
							echo "<td>".$this->condorArray[$i]['Webuser']."</td>";
							echo "<td>".date("d/m - H:i",$this->condorArray[$i]['JobStartDate'])."</td>";
							echo "<td>".date("H:i:s",$this->condorArray[$i]['CommittedTime']-60*60)."</td>";
							switch ($this->condorArray[$i]['JobStatus'])
							{
							case 0:
								echo "<td>Unexpanded</td>";
								break;
								
							case 1:
								echo "<td>Idle</td>";
								break;
								
							case 2:
								echo "<td>Running</td>";
								break;
								
							case 3:
								echo "<td>Removed</td>";
								break;
								
							case 4:
								echo "<td>Completed</td>";
								break;
								
							case 5:
								echo "<td>Held</td>";
								break;
								
							case 6:
								echo "<td>Submission error</td>";
								break;
							}
							echo "<td>".$this->condorArray[$i]['JobPrio']."</td>";
							echo "<td>".$this->condorArray[$i]['CoreSize']."</td>";
							echo "<td>".$this->condorArray[$i]['CMD']."</td>";
							
							if (($this->condorArray[$i]['Webuser'] == $user_name) || ($is_admin >= 1))
								echo "<td style='text-align: center;'><a class='mouse_hover'
								onclick=\"\$.ajax({
									url: '".$ajax_Page."',
									type: 'POST',
									data: {delete_submited_file: '".$this->condorArray[$i]['ClusterID']."'},
									success: function(result){\$('".$output_ID."').html(result);}
								})\"><i class='icon-trash'></i></a></td>";
							else
								echo "<td></td>";
						echo "</tr>";
					}
						
				echo "</tbody>
			</table>";
		}
	}
	
	// izpis navigacije strani
	public function drawPageNavigation($ajax_Page, $output_ID, $page_index)
	{
		echo "<div id='pagination_global' class='pagination pagination-small' style='margin-bottom:0px'>
			<ul>";
			
				echo "<li onclick=\"\$.ajax({url:'".$ajax_Page."',
				type:'POST',
				data:{".$page_index.":1},
				success:function(result){\$('".$output_ID."').html(result);}})\"
				class='".$this->pagePrevStatus."'><a href='#1'><<</a></li>";
						
				echo "<li onclick=\"\$.ajax({url:'".$ajax_Page."',
				type: 'POST',
				data: {".$page_index.":".$this->pagePrev."},
				success: function(result){\$('".$output_ID."').html(result);}
				})\" class='".$this->pagePrevStatus."'><a href='#".$this->pagePrev."'><</a></li>";
				
				for ($i=$this->minDisplayPage;$i<=$this->maxDisplayPage;$i++)
				{
					if($this->currentPage == $i)
						echo "<li onclick=\"\$.ajax({url:'".$ajax_Page."',
						type:'POST',
						data:{".$page_index.":".$i."},
						success:function(result){\$('".$output_ID."').html(result);}})\"
						class='active'><a href='#".$i."'>".$i."</a></li>";
					else
						echo "<li onclick=\"\$.ajax({url:'".$ajax_Page."',
						type:'POST',
						data:{".$page_index.":".$i."},
						success:function(result){\$('".$output_ID."').html(result);}})\"><a href='#".$i."'>".$i."</a></li>";
				}
					
				echo "<li onclick=\"\$.ajax({url:'".$ajax_Page."',
				type:'POST',
				data:{".$page_index.":".$this->pageNext."},
				success:function(result){\$('".$output_ID."').html(result);}})\"
				class='".$this->pageNextStatus."'><a href='#".$this->pageNext."'>></a></li>";

				echo "<li onclick=\"\$.ajax({url:'".$ajax_Page."',
				type:'POST',
				data:{".$page_index.":".$this->pageNumbers."},
				success:function(result){\$('".$output_ID."').html(result);}})\"
				class='".$this->pageNextStatus."'><a href='#".$this->pageNumbers."'>>></a></li>";
			
			echo "</ul>
		</div>";
	}
}
?>
