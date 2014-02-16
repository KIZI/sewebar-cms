using System;
using System.IO;
using System.Text;

namespace LMWrapper.LISpMiner
{
	public class LMGridPooler : LMPooler
	{
		// /RMCaseID:<RMCaseID>		... ReverseMiner CaseID to run all tasks in this case	
		// /ShutdownDelaySec:<n>		... (O) number of seconds <0;86400> before the LM TaskPooler server is shutted down after currently the last waiting task is solved (default: 10)
		// /TimeMaxHours:<n>		... (O) maximal number of hours the server is running (to allow for periodical re-start) (default: 1)
		// /Server				... (O) this instance becomes the server (the Task parameter is ignored)
		
		/// <summary>
		/// /GridBinariesPath:[Path]	... (O) an optional path to the PCGrid/Binaries directory (if not in a default location as subdirectory)
		/// </summary>
		public string GridBinariesPath
		{
			get { return this.GridSettings.Binaries; }
		}

		/// <summary>
		///	/GridDataPath:[Path]		... (O) an optional path to the PCGrid/Binaries directory (if not in a default location as subdirectory)
		/// </summary>
		public string GridDataPath { get; private set; }

		public override string TimeLog
		{
			get
			{
				if (this.LISpMiner.Environment.TimeLog)
				{
					return String.Format("{0}/{1}.dat", this.LISpMiner.LMPrivatePath, "_TimeLog_LMGridPooler");
				}

				return null;
			}
		}

		private PCGridSettings GridSettings { get; set; }

		public override string Arguments
		{
			get
			{
				var arguments = new StringBuilder("");

				if (!String.IsNullOrEmpty(this.OdbcConnectionString))
				{
					arguments.AppendFormat("/ODBCConnectionString=\"{0}\" ", this.OdbcConnectionString);
				}

				// /TaskID <TaskID>
				if (!String.IsNullOrEmpty(this.TaskId))
				{
					arguments.AppendFormat("/TaskID:{0} ", this.TaskId);
				}

				// /TaskName <TaskName>
				if (!String.IsNullOrEmpty(this.TaskName))
				{
					arguments.AppendFormat("\"/TaskName:{0}\" ", this.TaskName);
				}

				if (!String.IsNullOrEmpty(this.GridBinariesPath))
				{
					arguments.AppendFormat("\"/GridBinariesPath:{0}\" ", this.GridBinariesPath);
				}

				if (!String.IsNullOrEmpty(this.GridDataPath))
				{
					arguments.AppendFormat("\"/GridDataPath:{0}\" ", this.GridDataPath);
				}

				// /TaskCancel
				if (this.TaskCancel)
				{
					arguments.Append("/TaskCancel ");
				}

				// /CancelAll
				if (this.CancelAll)
				{
					arguments.Append("/CancelAll ");
				}

				// /TimeOut <sec>
				if (this.TimeOut != null)
				{
					arguments.AppendFormat("/TimeOut:{0} ", this.TimeOut);
				}

				// /ShutdownDelaySec:<n>
				if (this.ShutdownDelaySec != null)
				{
					arguments.AppendFormat("/ShutdownDelaySec:{0} ", this.ShutdownDelaySec);
				}

				// /Quiet
				if (this.Quiet)
				{
					arguments.Append("/Quiet ");
				}

				// /NoProgress
				if (this.NoProgress)
				{
					arguments.Append("/NoProgress ");
				}

				// /AppLog
				if (!String.IsNullOrEmpty(this.AppLog))
				{
					arguments.AppendFormat("\"/AppLog:{0}\" ", this.AppLog);
				}

				// /TimeLog
				if (!String.IsNullOrEmpty(this.TimeLog))
				{
					arguments.AppendFormat("\"/TimeLog:{0}\"", this.TimeLog);
				}

				return arguments.ToString().Trim();
			}
		}

		internal LMGridPooler(LISpMiner lispMiner, ODBC.ConnectionString connectionString, string lmPrivatePath, PCGridSettings gridSettings)
			: base()
		{
			this.LISpMiner = lispMiner;
			this.LMExecutablesPath = this.LISpMiner.LMExecutablesPath;
			this.LMPrivatePath = lmPrivatePath;
			this.OdbcConnectionString = connectionString.Value;

			this.GridSettings = gridSettings;
			this.GridDataPath = Path.GetFullPath(string.Format(@"{0}\PCGrid", this.LMPrivatePath));

			this.ApplicationName = "LMGridPooler.exe";
			this.AppLog = String.Format("{0}-{1}.dat", "_AppLog_LMGridPooler", Guid.NewGuid());
			this.CancelAll = false;
			// this.TimeOut = 10;

			InitializePCGrid();
		}

		private void InitializePCGrid()
		{
			if (!Directory.Exists(this.GridDataPath))
			{
				DirectoryInfo mainDirectory = Directory.CreateDirectory(this.GridDataPath);

				// Input
				mainDirectory.CreateSubdirectory("Input");

				// Output
				mainDirectory.CreateSubdirectory("Output");

				// Results
				mainDirectory.CreateSubdirectory("Results");

				// Temp
				mainDirectory.CreateSubdirectory("Temp");
			}

			CheckPCGridSettings();
		}

		private void CheckPCGridSettings()
		{
			// SewebarConnect.jks
			var keystore = this.GridSettings.KeyStore;

			if (!File.Exists(keystore))
			{
				throw new FileNotFoundException(string.Format("Keystore \"{0}\" does not exist.", keystore));
			}

			// SewebarConnect.grid.settings
			string main;
			
			if (this.LISpMiner.SharedPool)
			{
				main = Path.GetFullPath(string.Format(@"{0}\..\..\LISp Miner\PCGrid", this.GridSettings.Binaries));
			}
			else
			{
				main = this.GridDataPath;
			}

			string temp = string.Format(@"{0}\Temp", main);
			string filename = this.GridSettings.GetSettingsPath(main);

			if (!File.Exists(filename))
			{
				GenereatePCGridSettings(main, filename, keystore, temp);
			}
		}

		private void GenereatePCGridSettings(string main, string filename, string keystore, string temp)
		{
			var dir = Path.GetDirectoryName(filename);

			if (dir != null && !Directory.Exists(dir))
			{
				Directory.CreateDirectory(dir);
			}

			using (var filestream = new FileStream(filename, FileMode.Create))
			{
				using (var writer = new StreamWriter(filestream))
				{
					writer.WriteLine("#");
					writer.WriteLine("# Grid Client config file");
					writer.WriteLine("#");
					writer.WriteLine("");
					writer.WriteLine("# keystore filename");
					writer.WriteLine("keystore={0}", keystore);
					writer.WriteLine("");
					writer.WriteLine("# key alias in the keystore");
					writer.WriteLine("alias={0}", this.GridSettings.Alias);
					writer.WriteLine("");
					writer.WriteLine("# keystore & key password");
					writer.WriteLine("# It is HIGHLY recommended not to write password here, but instead");
					writer.WriteLine("# use the password dialog below!");
					writer.WriteLine("password={0}", this.GridSettings.Password);
					writer.WriteLine("");
					writer.WriteLine("# Password dialog class, a graphical dialog is used to ask the ");
					writer.WriteLine("# password from the user");
					writer.WriteLine("#passworddialog=fi.techila.grid.management.oma.client.GraphicalPasswordDialog");
					writer.WriteLine("");
					writer.WriteLine("# Status window class, a graphical progress bar.");
					writer.WriteLine("# Uncomment to use.");
					writer.WriteLine("#statuswindow=fi.techila.grid.management.oma.client.GraphicalStatusWindow");
					writer.WriteLine("");
					writer.WriteLine("# StatusWindow implementation specific configuration:");
					writer.WriteLine("# Leave progress bar window open if the projects have errors (true/false)");
					writer.WriteLine("statuswindow.nocloseonerror=false");
					writer.WriteLine("");
					writer.WriteLine("# server hostname");
					writer.WriteLine("hostname={0}", this.GridSettings.Hostname);
					writer.WriteLine("");
					writer.WriteLine("# server port");
					writer.WriteLine("port={0}", this.GridSettings.Port);
					writer.WriteLine("");
					writer.WriteLine("# temporary directory, use absolute path here, especially with Matlab");
					writer.WriteLine("tempdir={0}", temp);
					writer.WriteLine("");
					writer.WriteLine("#");
					writer.WriteLine("# Error file, if defined project errors are appended to the given file.");
					writer.WriteLine("#");
					writer.WriteLine("#errorfile={0}\\errorfeed", temp);
					writer.WriteLine("");
					writer.WriteLine("# Error directory, projects errors are appended to one file per project");
					writer.WriteLine("# in this directory");
					writer.WriteLine("errordir={0}\\Error", temp);
					writer.WriteLine("");
					writer.WriteLine("# if true the project errors are printed to STDERR (console)");
					writer.WriteLine("#stderr=true");
					writer.WriteLine("");
					writer.WriteLine("# file where all stdoutputs from the clients are fed.");
					writer.WriteLine("#stdoutfile={0}\\gridout.log", temp);
					writer.WriteLine("");
					writer.WriteLine("# directory where stdoutputs from the clients are fed. Each project");
					writer.WriteLine("# will have a subdirectory created.");
					writer.WriteLine("stdoutdir={0}\\Output", main);
					writer.WriteLine("");
					writer.WriteLine("");
					writer.WriteLine("#");
					writer.WriteLine("# Some fine-tuning parameters, defaults are usually ok.");
					writer.WriteLine("#");
					writer.WriteLine("# Poll time, wait between polling project status from the server.");
					writer.WriteLine("# In milliseconds.");
					writer.WriteLine("#polltime=5000");
					writer.WriteLine("");
					writer.WriteLine("# Wait between download retries (when project is done but the result is not");
					writer.WriteLine("# yet ready for download). In milliseconds.");
					writer.WriteLine("#dlretrytime=10000");
					writer.WriteLine("");
					writer.WriteLine("# Download results immediately after they are available");
					writer.WriteLine("transfermode=stream");
					writer.WriteLine("");
					writer.WriteLine("#");
					writer.WriteLine("# Logging config");
					writer.WriteLine("#");
					writer.WriteLine("# logfile");
					writer.WriteLine("logfile={0}\\Log\\grid.log", temp);
					writer.WriteLine("");
					writer.WriteLine("# max size of the log file (in bytes)");
					writer.WriteLine("maxlogsize=1000000");
					writer.WriteLine("");
					writer.WriteLine("# max number of logfiles");
					writer.WriteLine("maxlogfiles=10");
					writer.WriteLine("");
					writer.WriteLine("# Logging level config");
					writer.WriteLine("# possible values are: ");
					writer.WriteLine("#   ALL");
					writer.WriteLine("#   SEVERE (highest value) ");
					writer.WriteLine("#   WARNING ");
					writer.WriteLine("#   INFO ");
					writer.WriteLine("#   CONFIG ");
					writer.WriteLine("#   FINE ");
					writer.WriteLine("#   FINER ");
					writer.WriteLine("#   FINEST (lowest value)");
					writer.WriteLine("#   OFF");
					writer.WriteLine("");
					writer.WriteLine("# logging level to the file ");
					writer.WriteLine("fileloglevel=WARNING");
					writer.WriteLine("");
					writer.WriteLine("# logging level to console");
					writer.WriteLine("consoleloglevel=SEVERE");

					writer.Close();
				}
			}
		}
	}  
}
