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
			$result_array[0] = "Polje username je prazno.";
		}
		if ($this->checkUsernameExistance($username)) // preveri obstoj username
		{
			$result_array[1] = "Username ze obstaja.";
		}
		if (3 > strlen($username) && $username != "") // preveri dolzino username
		{
			$result_array[2] = "Prekratek username.";
		}

		return $result_array;
	}

	// preveri vse kontrole za password
	public function passwordControl($password)
	{
		$result_array = array();

		if ($password == "") // preveri,ce je polje zapolnjeno
		{
			$result_array[0] = "Polje password je prazno.";
		}
		if (5 > strlen($password) && $password != "") // preveri dolzino password
		{
			$result_array[1] = "Prekratek password.";
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
			$result_array[1] = "Email ze obstaja.";
		}
		if (3 > strlen($email) && $email != "") // preveri dolzino email
		{
			$result_array[2] = "Prekratek email.";
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
				$result_array[0] = "Uspesno registrirano.";
			}
			else
			{
				$result_array[1] = "Prislo je do napake pri vnosu.";
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
				$result_array[0] = "Username spremenjen.";
			}

			// password urejanje
			if (empty($result_array[1]))
			{
				$query = "UPDATE users SET password = PASSWORD('".$new_password."') WHERE userid = ".$this->userid;
				$result = mysql_query($query, $this->dblink);
				$password = $new_password;
				$this->loginUser($user_name, $password);
				$result_array[1] = "Password spremenjen.";
			}

			// email urejanje
			if (empty($result_array[2]))
			{
				$query = "UPDATE users SET email = '".$new_email."' WHERE userid = ".$this->userid;
				$result = mysql_query($query, $this->dblink);
				$result_array[2] = "Email spremenjen.";
			}

		}
		else
		{
			$result_array[0] = "Napacen username ali password.";
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
			$result_array[0] = "Username spremenjen.";
		}

		// password checking
		if (empty($result_array[1]))
		{
			$query = "UPDATE users SET password = PASSWORD('".$new_password."') WHERE username = '".$user_name."'";
			$result = mysql_query($query, $this->dblink);
			$result_array[1] = "Password spremenjen.";
		}

		// email checking
		if (empty($result_array[2])) // preveri novi email
		{
			$query = "UPDATE users SET email = '".$new_email."' WHERE username = '".$user_name."'";
			$result = mysql_query($query, $this->dblink);
			$result_array[2] = "Email spremenjen.";
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
			}
			else
			{
				$_SESSION['access'] = "time_out";
				unset($_SESSION['login_id']);
				unset($_SESSION['username']);
				unset($_SESSION['password']);
				unset($_SESSION['email']);
				unset($_SESSION['daysleft']);
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
		$query = "DELETE FROM users WHERE username = '".$username."'";
		$result = mysql_query($query, $this->dblink);
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
		echo "ni dokoncano";
	}
}

/* RAZRED ZA FILE MANAGEMENT */

class FileManager
{
	// spremenljivke
	private $root = "";

	// constructor postavi root za brskanje file-ov in ustvari privzete mape za prijavljenega uporabnika, ce se ne obstajajo
	public function __construct($lroot = "")
	{
		$this->root = $lroot.$_SESSION['login_id']."/";

		//pregleda, ce obstajajo vsi potrebni direktoriji za uporabnika - ce ne, jih zgenerira
		$this->makeDir("uploads");
		$this->makeDir("results");
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

	// ustvari directory
	public function makeDir($directory)
	{
		if (!is_dir($Dir = $this->root.$directory))
		{
			mkdir($Dir, 0777, true);
		}
	}

	// uploada file
	public function uploadFile($temp_file, $file, &$output)
	{
		if (!preg_match("/[^a-z0-9_.]/i", $file))
		{
			if (file_exists($this->root."uploads/".$file))
			{
				$output = "Datoteka ".$file." ze obstaja!";
			}
			else
			{
				move_uploaded_file($temp_file, $this->root."uploads/".$file);
				$output = "Datoteka ".$file." uspesno prenesena. Nahaja se v uploads mapi.";

				//razpakira zip file
				$fullFileName = pathinfo($this->root."uploads/".$file);
				$zipAarray = array("zip");
				if (in_array($fullFileName['extension'], $zipAarray))
				{
					$this->unzipFile("uploads/".$file);
					$this->removeFile("uploads/".$file);
				}
			}
		}
		else
		{
			$output = "Ime datoteke lahko vsebuje samo stevilke, crke in znaka _ ter .";
		}
	}
	
	// odzipa file
	public function unzipFile($file)
	{
		$zip = new ZipArchive;
		$res = $zip->open($this->root.$file);
		$dirFullName = pathinfo($this->root.$file);

		if ($res === TRUE)
		{
			$zip->extractTo($dirFullName['dirname']."/".$dirFullName['filename']);
			$zip->close();
			$this->removeFile($file);
			return true;
		}
		else
		{
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
	
	// ustvari submit file
	public function createSubmitFile($exec_file, $username, &$output)
	{
		if(!preg_match("/[^a-z0-9_.]/i", $exec_file))
		{
			if (file_exists($this->root."uploads/".$exec_file.".submit"))
			{
				$output = "Datoteka ".$exec_file.".submit ze obstaja.";
			}
			else
			{
				$file = fopen($this->root."uploads/".$exec_file.".submit","x+");
			
					//string za vpisat v submit datoteko
					$string="Universe=vanilla
					Executable=".$this->root."uploads/".$exec_file."
					Output=".$this->root."results/".$exec_file.".output
					Error=".$this->root."results/".$exec_file.".error
					Log=".$this->root."results/".$exec_file.".log
					+Owner = \"".$username."\"

					should_transfer_files = YES
					when_to_transfer_output = ON_EXIT

					queue";
				
					$trimmedString=str_replace("\t","",$string);
					
					fwrite($file,$trimmedString);
			
				fclose($file);
				
				$output = "Datoteka ".$exec_file.".submit uspesno ustvarjena. Nahaja se v uploads mapi.";
			}
		}
		else
		{
			$output = "Ime submit datoteke lahko vsebuje samo stevilke, crke in znaka _ ter .";
		}
	}

	// submitaj file v condor
	public function submitFile($files, &$output)
	{
		if (is_array($files))
		{
			foreach ($files as $key => $value)
			{
				$file_name = pathinfo($value);
				
				if(!file_exists($this->root.$value))
				{
					$output[$key] = "Datoteka ".$file_name['basename']." vec ne obstaja!";
				}
				elseif(preg_match("/[^a-z0-9_.]/i", $file_name['basename']))
				{
					$output[$key] = "Ime submitane datoteke lahko vsebuje samo stevilke, crke in znaka _ ter .";
				}
				else
				{
					condor_submit($this->root.$value, $submitOut);
					$output[$key] = $submitOut;
				}
			}
		}
		else
		{
			$file_name = pathinfo($files);
			
			if(!file_exists($this->root.$files))
			{
				$output['submit'] = "Datoteka ".$file_name['basename']." vec ne obstaja!";
			}
			elseif(preg_match("/[^a-z0-9_.]/i", $file_name['basename']))
			{
				$output[$key] = "Ime submitane datoteke lahko vsebuje samo stevilke, crke in znaka _ ter .";
			}
			else
			{
				condor_submit($this->root.$files, $submitOut);
				$output['submit'] = $submitOut;
			}
		}
	}

	// izpise vse file v podanem direktoriju
	public function displayFolders($directory = "")
	{

		$scanDir = $this->scanDir($directory);
		$dirFullName = pathinfo($this->root.$directory);
		$dirName = $dirFullName['basename'];
		$directoryArray = splitString($directory, "/");
		$iter = "";

		// prva vrstica za navigacijo po folderjih
		echo "<form method='post' id='file_form' enctype='multipart/form-data'>
			<ul class='breadcrumb'>
				<li><a class='mouse_hover' onclick=\"goToPath('')\">root</a> <span class='divider'>/</span></li>";
				if ($directoryArray[0] != "")
				{
					foreach ($directoryArray as $key => $value)
					{
						$iter .= $value."/";
						echo "<li><a class='mouse_hover' onclick=\"goToPath('".$iter."/')\">".$value."</a> <span class='divider'>/</span></li>";
					}
				}
			echo "</ul>";

			// tabela za file management
			echo "<table id='file_table' class='table table-condensed'><thead>
				<tr>
					<th class='span4'>Filename</td>
					<th class='span3'>Filetype</td>
					<th class='span1' style='text-align: center;'>Submit</td>
					<th class='span1' style='text-align: center;'>Delete</td>
				</tr></thead><tbody>";

				//izpise vse folderje
				for ($i=0; $i<count($scanDir); $i++)
				{
					$fullFileName = pathinfo($scanDir[$i]);
	
					if(is_dir($this->root.$directory.$scanDir[$i]))
					{

						echo "<tr>
							<td><i class='icon-folder-open'></i> <a class='mouse_hover' onclick=\"goToPath('".$directory.$scanDir[$i]."/')\">".$scanDir[$i]."</a></td>
							<td><div>folder</div></td>
							<td></td>
							<td style='text-align: center;'><a class='mouse_hover' onclick=\"submitFormAjaxDelete('".$directory.$fullFileName['basename']."')\"><i class='icon-trash'></i></a></td>
						</tr>";

					}
				}

				//izpise .condor in .submit file
				for ($i=0; $i<count($scanDir); $i++)
				{
					$fullFileName = pathinfo($scanDir[$i]);
		
					if($fullFileName['extension'] == "submit" || $fullFileName['extension'] == "condor")
					{

						echo "<tr>
							<td><i class='icon-file'></i> <a href='/CondorUI".ltrim($this->root, ".").$directory.$fullFileName['basename']."'>".$fullFileName['basename']."</a></td>
							<td>".$fullFileName['extension']."</td>
							<td style='text-align: center;'><a class='mouse_hover' onclick=\"submitFormAjaxSubmit('".$directory.$fullFileName['basename']."')\"><i class='icon-upload'></i></a></td>
							<td style='text-align: center;'><a class='mouse_hover' onclick=\"submitFormAjaxDelete('".$directory.$fullFileName['basename']."')\"><i class='icon-trash'></i></a></td>
						</tr>";

					}
				}

				//izpise vse ostale file
				for ($i=0; $i<count($scanDir); $i++)
				{
					$fullFileName = pathinfo($scanDir[$i]);
		
					if(!is_dir($this->root.$directory.$scanDir[$i]) && $fullFileName['extension'] != "submit" && $fullFileName['extension'] != "condor")
					{

						echo "<tr>
							<td><i class='icon-file'></i> <a href='/CondorUI".ltrim($this->root, ".").$directory.$fullFileName['basename']."'>".$fullFileName['basename']."</a></td>
							<td>".$fullFileName['extension']."</td>
							<td></td>
							<td style='text-align: center;'><a class='mouse_hover' onclick=\"submitFormAjaxDelete('".$directory.$fullFileName['basename']."')\"><i class='icon-trash'></i></a></td>
						</tr>";

					}
				}
				
				if (count($scanDir) == 0)
				{
					echo "<tr><td colspan='4'>Mapa je prazna.</td></tr>";
				}
			
				// zadnja vrstica za zakljucek
			echo "</tbody></table>
			<input type='file' name='file[]' id='advanced_file_upload' multiple/ style='visibility:hidden;float:right;'>
		</form>";
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
?>
