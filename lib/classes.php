<?php
include_once "functions.php";

/* Razred za user management */
class UserManager
{
	// spremenljivke
	protected $dblink = "";
	private $userid = "";
	private $username = "";
	private $password = "";
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
	
	// vnese novega uporabnika v bazo
	public function inputNewUser($user_name, $password, $email, $is_admin, $register_time, $active_time)
	{
		$query = "SELECT * FROM users WHERE username = '".$user_name."'";
		$result = mysql_query($query, $this->dblink);

		if (mysql_num_rows($result) >= 1 || (3 > strlen($user_name)) || (5 > strlen($password)) || (5 > strlen($email)))
		{
			return false;
		}
		else
		{
			// vstavi v bazo podatkov
			$query = "INSERT INTO users VALUES (NULL,'$user_name',PASSWORD('$password'),'$email','$is_admin','$register_time','$active_time')";
			$result = mysql_query($query, $this->dblink);
			return $query;

			if ($result)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	// spremeni uporabnisko ime obstojecega uporabnika
	public function editUser($user_name, $password, $new_user_name)
	{
		if($this->selectUser($user_name, $password))
		{
			$query = "UPDATE users SET username = ".$new_user_name." WHERE id = ".$this->userid;
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
		else
		{
			return false;
		}
	}

	// spremeni password obstojecega uporabnika
	public function editPassword($user_name, $password, $new_password)
	{
		if($this->selectUser($user_name, $password))
		{
			$query = "UPDATE users SET password = PASSWORD('".$new_password."') WHERE id = ".$this->userid;
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
		else
		{
			return false;
		}
	}

	// spremeni email obstojecega uporabnika
	public function editEmail($user_name, $password, $new_email)
	{
		if($this->selectUser($user_name, $password))
		{
			$query = "UPDATE users SET email = ".$new_email." WHERE id = ".$this->userid;
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
		else
		{
			return false;
		}
	}

	// preveri, koliko dni ima se oseba na razpolago
	public function daysLeft($user_name, $password)
	{
		$this->selectUser($user_name, $password);

		if (($this->dayspassed <= $this->activetime) || ($this->activetime == 0))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	// ustvari session za uporabnika
	public function loginUser($user_name, $password)
	{
		$this->selectUser($user_name, $password);
		
		if ($this->daysLeft($user_name, $password))
		{
			$_SESSION['access'] = $this->accesscontrol;
			$_SESSION['login_id'] = $this->userid;
			$_SESSION['username'] = $this->username;
			$_SESSION['password'] = $this->password;
			$_SESSION['daysleft'] = $this->daysleft;
		}
		else
		{
			$_SESSION['access'] = "no_access";
			unset($_SESSION['login_id']);
			unset($_SESSION['username']);
			unset($_SESSION['password']);
			unset($_SESSION['daysleft']);
		}
	}

	// izbrise session za uporabnika
	public function logoutUser()
	{
		$_SESSION['access'] = "login";
		unset($_SESSION['login_id']);
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		unset($_SESSION['daysleft']);
	}

	// izpise vse uporabnike v tabeli
	public function displayUsers()
	{
		echo "ni dokoncano";
	}
}

/* Razred za file management */

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
		if (file_exists($this->root."uploads/".$file))
		{
			$output = "Datoteka ".$file." ze obstaja!";
		}
		else
		{
			move_uploaded_file($temp_file, $this->root."uploads/".$file);
			$output = "Datoteka uspesno prenesena. Nahaja se v uploads mapi.";

			//razpakira zip file
			$fullFileName = pathinfo($this->root."uploads/".$file);
			$zipAarray = array("zip","rar","tar","gz","7z");
			if (in_array($fullFileName['extension'], $zipAarray))
			{
				$this->unzipFile("uploads/".$file);
				$this->removeFile("uploads/".$file);
			}
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
	public function createSubmitFile($exec_file, &$output)
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

				should_transfer_files = YES
				when_to_transfer_output = ON_EXIT

				queue";
				
				$trimmedString=str_replace("\t","",$string);
					
				fwrite($file,$trimmedString);
			
			fclose($file);
		}
	}

	// submitaj file v condor
	public function submitFile($files, &$output1, &$output2)
	{
		foreach ($files as $key => $value)
		{
			if(!file_exists($this->root.$value))
			{
				$output1[$key] = "Datoteka ".$value." vec ne obstaja!";
			}
			else
			{
				condor_submit($this->root.$value, $submitOut);
				$output2[$key] = $submitOut;
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
		echo "<table id='file_table' style='margin-right:10px;'>
			<tr>
				<td colspan='4' style='padding: 5px;'><span class='file_navigation_button' style='cursor: pointer;' onclick=goToPath('')>root</span> / ";
					foreach ($directoryArray as $key => $value)
					{
						$iter .= $value."/";
						echo "<span class='file_navigation_button' style='cursor: pointer;' onclick=goToPath('".$iter."/')>".$value."</span> / ";
					}
				echo "</td>
			</tr>
			<tr>
				<td style='padding: 5px;'>filename</td>
				<td>filetype</td>
				<td>submit</td>
				<td>delete</td>
			</tr>";

			//izpise vse folderje
			for ($i=0; $i<count($scanDir); $i++)
			{
				$fullFileName = pathinfo($scanDir[$i]);
	
				if(is_dir($this->root.$directory.$scanDir[$i]))
				{

					echo "<tr style='background-color: white'>
						<td style='text-align:left; min-width: 100px;'><span class='file_navigation_button' style='cursor: pointer;' onclick=goToPath('".$directory.$scanDir[$i]."/')>".$scanDir[$i]." /</span></td>
						<td style='min-width: 60px';>folder</td>
						<td></td>
						<td><input type='checkbox' class='delete_checkbox' name='delete_file[]' value='".$directory.$fullFileName['basename']."'></td>
					</tr>";

				}
			}

			//izpise .condor in .submit file
			for ($i=0; $i<count($scanDir); $i++)
			{
				$fullFileName = pathinfo($scanDir[$i]);
		
				if($fullFileName['extension'] == "submit" || $fullFileName['extension'] == "condor")
				{

					echo "<tr style='background-color: white'>
						<td style='text-align:left;'><a class='file_navigation_button' href='/CondorUI".ltrim($this->root, ".").$directory.$fullFileName['basename']."'>".$fullFileName['basename']."</a></td>
						<td>".$fullFileName['extension']."</td>
						<td><input type='checkbox' class='submit_checkbox' name='submit_file[]' value='".$directory.$fullFileName['basename']."' /></td>
						<td><input type='checkbox' class='delete_checkbox' name='delete_file[]' value='".$directory.$fullFileName['basename']."'></td>
					</tr>";

				}
			}

			//izpise vse ostale file
			for ($i=0; $i<count($scanDir); $i++)
			{
				$fullFileName = pathinfo($scanDir[$i]);
		
				if(!is_dir($this->root.$directory.$scanDir[$i]) && $fullFileName['extension'] != "submit" && $fullFileName['extension'] != "condor")
				{

					echo "<tr style='background-color: white'>
						<td style='text-align:left;'><a class='file_navigation_button' href='/CondorUI".ltrim($this->root, ".").$directory.$fullFileName['basename']."'>".$fullFileName['basename']."</a></td>
						<td>".$fullFileName['extension']."</td>
						<td></td>
						<td><input type='checkbox' class='delete_checkbox' name='delete_file[]' value='".$directory.$fullFileName['basename']."'></td>
					</tr>";

				}
			}
			
			// zadnja vrstica za oznacevanje
			echo "<tr>
				<td colspan='2' style='text-align:right; padding: 5px;'>Select All:</td>
				<td><input type='checkbox' name='select_all_submits' value='false' class='select_all_submits' /></td>
				<td><input type='checkbox' name='select_all_deletes' value='false' class='select_all_deletes' /></td>
			</tr>
		</table>";
	}

}

/* Razred za stats tracking */
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
