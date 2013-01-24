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
				$_SESSION['isadmin'] = $this->isadmin;
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
	public function uploadFile($temp_file, $file, $username, &$output)
	{
		if (!preg_match("/[^a-z0-9_.-]/i", $file))
		{
			if (file_exists($this->root."uploads/".$file))
			{
				$output = "Datoteka ".$file." ze obstaja!";
			}
			else
			{
				move_uploaded_file($temp_file, $this->root."uploads/".$file);
				$this->correctSubmitFile($this->root.$files, $user_name);
				
				$output = "Datoteka ".$file." uspesno prenesena. Nahaja se v uploads mapi.";
				$fullFileName = pathinfo($this->root."uploads/".$file);
				
				//Popravi submit file
				$submitArray = array("submit","condor");
				if (in_array($fullFileName['extension'], $submitArray))
					$this->correctSubmitFile($this->root."uploads/".$file, $username);
				
				//razpakira zip file
				$zipArray = array("zip");
				if (in_array($fullFileName['extension'], $zipArray))
				{
					$this->unzipFile("uploads/".$file);
					$this->removeFile("uploads/".$file);
				}
			}
		}
		else
		{
			$output = "Ime datoteke lahko vsebuje samo stevilke, crke in znake _ . -";
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
		if(!preg_match("/[^a-z0-9_.-]/i", $exec_file))
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
			$output = "Ime submit datoteke lahko vsebuje samo stevilke, crke in znake _ . -";
		}
	}
	
	// preveri poti v mapah in owner status submit datoteke
	public function correctSubmitFile($submitFile, $username)
	{
		$dirPath = pathinfo($submitFile);
	
		$readFile = fopen($submitFile,"r");
		$writeFile = fopen($submitFile.".out","w");
		
		fwrite($writeFile, "+Owner=\"".$username."\"".PHP_EOL);
		
		while(!feof($readFile))
		{
			$line = fgets($readFile);
			
			if (strpos($line, "Executable") !== false)
			{
				$fileName = substr(strstr($line, '='), 1);
				$line = "Executable=".$dirPath['dirname']."/".$fileName;
			}
			elseif (strpos($line, "Output") !== false) 
			{
				$fileName = substr(strstr($line, '='), 1);
				$fileNamePath = pathinfo($fileName);
				mkdir(str_replace("uploads", "results", $dirPath['dirname'])."/".$fileNamePath['dirname'], 0777, true);
				$line = "Output=".str_replace("uploads", "results", $dirPath['dirname'])."/".$fileName;
			}
			elseif (strpos($line, "Error") !== false)
			{
				$fileName = substr(strstr($line, '='), 1);
				$fileNamePath = pathinfo($fileName);
				mkdir(str_replace("uploads", "results", $dirPath['dirname'])."/".$fileNamePath['dirname'], 0777, true);
				$line = "Error=".str_replace("uploads", "results", $dirPath['dirname'])."/".$fileName;
			}
			elseif (strpos($line, "Log") !== false)
			{
				$fileName = substr(strstr($line, '='), 1);
				$fileNamePath = pathinfo($fileName);
				mkdir(str_replace("uploads", "results", $dirPath['dirname'])."/".$fileNamePath['dirname'], 0777, true);
				$line = "Log=".str_replace("uploads", "results", $dirPath['dirname'])."/".$fileName;
			}
			elseif (strpos($line, "Owner") !== false)
			{
				$line = "";
			}
			
			fwrite($writeFile, $line);
		}
	
		fclose($readFile);
		fclose($writeFile);
		
		unlink($submitFile);
		rename($submitFile.".out", $submitFile);
	}

	// submitaj file v condor
	public function submitFile($files, $user_name, &$output)
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
				elseif(preg_match("/[^a-z0-9_.-]/i", $file_name['basename']))
				{
					$output[$key] = "Ime submitane datoteke lahko vsebuje samo stevilke, crke in znake _ . -";
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
			elseif(preg_match("/[^a-z0-9_.-]/i", $file_name['basename']))
			{
				$output[$key] = "Ime submitane datoteke lahko vsebuje samo stevilke, crke in znake _ . -";
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
		if (($this->currentPage - 4) > 1)
		{
			$this->minDisplayPage = $this->currentPage - 4;
			$this->minDisplayCont = true;
		}
		else
		{
			$this->minDisplayPage = 1;
			$this->minDisplayCont = false;
		}
		
		if (($this->currentPage + 4) < $this->pageNumbers)
		{
			$this->maxDisplayPage = $this->currentPage + 4;
			$this->maxDisplayCont = true;
		}
		else
		{
			$this->maxDisplayPage = $this->pageNumbers;
			$this->maxDisplayCont = false;
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

	//home last submits
	public function drawLastSubmitTable($ajax_Page, $menu_info, $output_ID)
	{
		if(empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Vas condor queue je prazen!</strong></div>";
		}
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Date</th>
						<th>Status</th>
						<th>Delete</th>";
					echo "</tr>
				</thead>
				<tbody>";
				
					$tempCluster = "";
				
					// for zanka, ki gre skozi vse elelmente, ki bojo izpisani
					for ($i=$this->minPage;$i<$this->maxPage;$i++)
					{
						if ($tempCluster != $this->condorArray[$i]['ClusterId'])
						{
							echo "<tr>";
								echo "<td>".$this->condorArray[$i]['ClusterId']."</td>";
								echo "<td>".$this->condorArray[$i]['Cmd']."</td>";
								echo "<td>".date("d/m - H:i",$this->condorArray[$i]['JobStartDate'])."</td>";
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
								echo "<td style='text-align: center;'><a class='mouse_hover' onclick=\"ajaxCondorDelete('".$ajax_Page."','".$menu_info."','".$this->condorArray[$i]['ClusterId']."','".$output_ID."')\"><i class='icon-trash'></i></a></td>";
							echo "</tr>";
							
							$tempCluster = $this->condorArray[$i]['ClusterId'];
						}
					}
					
				echo "</tbody>
			</table>";
		}
	}
	
	//home computer status
	public function drawComputerStatusTable($ajax_Page, $menu_info, $output_ID)
	{
		if (empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Condor pool je prazen!</strong></div>";
		}	
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th>Name</th>
						<th>State</th>
						<th>Activity</th>
					</tr>
				</thead>
				<tbody>";		

					foreach ($this->condorArray as $key => $value)
					{
						echo "<tr>
							<td>".$value['Name']."</td>
							<td>".$value['State']."</td>
							<td>".$value['Activity']."</td>
						</tr>";
					}
								
				echo "</tbody>
			</table>";
		}
	}
	
	//advanced condor queue
	public function drawCondorQTable($ajax_Page, $menu_info, $output_ID, $user_name, $is_admin)
	{
		if(empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Condor queue je prazen!</strong></div>";
		}
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th>ID</th>
						<th>Owner</th>
						<th>Submitted</th>
						<th>Run Time</th>
						<th>State</th>
						<th>Priority</th>
						<th>Size</th>
						<th>Name</th>
						<th>Delete</th>";
					echo "</tr>
				</thead>
				<tbody>";
	
					// for zanka, ki gre skozi vse elelmente, ki bojo izpisani
					for ($i=$this->minPage;$i<$this->maxPage;$i++)
					{
						echo "<tr>";
							echo "<td>".$this->condorArray[$i]['ClusterID'].".".$this->condorArray[$i]['ProcID']."</td>";
							echo "<td>".$this->condorArray[$i]['Owner']."</td>";
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
							echo "<td>".$this->condorArray[$i]['ExecutableSize']."</td>";
							echo "<td>".$this->condorArray[$i]['CMD']."</td>";
							
							if (($this->condorArray[$i]['Owner'] == $user_name) || ($is_admin >= 1))
								echo "<td style='text-align: center;'><a class='mouse_hover' onclick=\"ajaxCondorDelete('".$ajax_Page."','".$menu_info."','".$this->condorArray[$i]['ClusterID'].".".$this->condorArray[$i]['ProcID']."','".$output_ID."')\"><i class='icon-trash'></i></a></td>";
							else
								echo "<td></td>";
						echo "</tr>";
					}
						
				echo "</tbody>
			</table>";
		}
	}
	
	//advanced condor status
	public function drawCondorStatusTable($ajax_Page, $menu_info, $output_ID)
	{
		if(empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Condor pool je prazen!</strong></div>";
		}
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th>Name</th>
						<th>Operating Sys</th>
						<th>Architecture</th>
						<th>State</th>
						<th>Activity</th>
						<th>Load</th>
						<th>Memory</th>";
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
	
	//advanced condor status total
	public function drawCondorStatusTotalTable($ajax_Page, $menu_info, $output_ID)
	{
		if(empty($this->condorArray))
		{
			echo "<div style='width:100%; text-align:center'><strong>Condor pool je prazen!</strong></div>";
		}
		else
		{
			echo "<table class='table table-condensed'>
				<thead>
					<tr>
						<th></th>
						<th>Total</th>
						<th>Claimed</th>
						<th>Unclaimed</th>";
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
	
	// izpis navigacije strani
	public function drawPageNavigation($ajax_Page, $menu_info, $output_ID, $page_index)
	{
		echo "<div class='pagination' style='margin-bottom:0px'>
			<ul>
				<li onclick=\"$.ajax({url:'".$ajax_Page."', type:'POST', data:{menu:'".$menu_info."', ".$page_index.":1}, success:function(result){\$('".$output_ID."').html(result);}})\" class='".$this->pagePrevStatus."'><a href=''><<</a></li>
				<li onclick=\"$.ajax({url:'".$ajax_Page."', type:'POST', data:{menu:'".$menu_info."', ".$page_index.":".$this->pagePrev."}, success:function(result){\$('".$output_ID."').html(result);}})\" class='".$this->pagePrevStatus."'><a href=''>Prev</a></li>";
				if($this->minDisplayCont)
					echo "<li class='disabled'><a href=''>...</a></li>";
				for ($i=$this->minDisplayPage;$i<=$this->maxDisplayPage;$i++)
				{
					if($this->currentPage == $i)
						echo "<li onclick=\"$.ajax({url:'".$ajax_Page."', type:'POST', data:{menu:'".$menu_info."', ".$page_index.":".$i."}, success:function(result){\$('".$output_ID."').html(result);}})\" class='active'><a href=''>".$i."</a></li>";
					else
						echo "<li onclick=\"$.ajax({url:'".$ajax_Page."', type:'POST', data:{menu:'".$menu_info."', ".$page_index.":".$i."}, success:function(result){\$('".$output_ID."').html(result);}})\"><a href=''>".$i."</a></li>";
				}
				if($this->maxDisplayCont)
					echo "<li class='disabled'><a href=''>...</a></li>";
				echo "<li onclick=\"$.ajax({url:'".$ajax_Page."', type:'POST', data:{menu:'".$menu_info."', ".$page_index.":".$this->pageNext."}, success:function(result){\$('".$output_ID."').html(result);}})\" class='".$this->pageNextStatus."'><a href=''>Next</a></li>
				<li onclick=\"$.ajax({url:'".$ajax_Page."', type:'POST', data:{menu:'".$menu_info."', ".$page_index.":".$this->pageNumbers."}, success:function(result){\$('".$output_ID."').html(result);}})\" class='".$this->pageNextStatus."'><a href=''>>></a></li>
			</ul>
		</div>";
	}
}
?>
