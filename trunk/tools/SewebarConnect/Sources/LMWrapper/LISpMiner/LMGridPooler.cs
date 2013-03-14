using System;
using System.IO;
using System.Text;

namespace LMWrapper.LISpMiner
{
	public class LMGridPooler : Executable, ITaskLauncher
	{
		// /RMCaseID:<RMCaseID>		... ReverseMiner CaseID to run all tasks in this case	
		// /ShutdownDelaySec:<n>		... (O) number of seconds <0;86400> before the LM TaskPooler server is shutted down after currently the last waiting task is solved (default: 10)
		// /TimeMaxHours:<n>		... (O) maximal number of hours the server is running (to allow for periodical re-start) (default: 1)
		// /Server				... (O) this instance becomes the server (the Task parameter is ignored)
		
		/// <summary>
		/// /TaskID [TaskID]		... TaskID of selected task
		/// </summary>
		public string TaskId { get; set; }

		/// <summary>
		/// /TaskName:[TaskName]		... Task.Name of the selected task
		/// </summary>
		public string TaskName { get; set; }

		public int KeepAlive { get; set; }

		/// <summary>
		/// /TaskCancel			... (O) to cancel task of given TaskID or name (if already running) or to remove it from queue
		/// </summary>
		public bool TaskCancel { get; set; }

		/// <summary>
		/// /CancelAll			... (O) to cancel any running task and to empty the queue
		/// </summary>
		public bool CancelAll { get; set; }

		/// <summary>
		/// /TimeOut:[sec]			... (O) time-out in seconds (approx.) after a task generation (excluding initialisation) is automatically interrupted
		/// </summary>
		public int? TimeOut { get; set; }

		/// <summary>
		/// /GridBinariesPath:[Path]	... (O) an optional path to the PCGrid/Binaries directory (if not in a default location as subdirectory)
		/// </summary>
		public string GridBinariesPath { get; private set; }

		protected string KeyStorePath
		{
			get { return this.GridBinariesPath + "\\..\\.."; }
		}

		public override string Arguments
		{
			get
			{
				var arguments = new StringBuilder("");

				if (!String.IsNullOrEmpty(this.Dsn))
				{
					arguments.AppendFormat("/DSN:{0} ", this.Dsn);
				}

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
					arguments.AppendFormat("\"/AppLog:{0}\"", this.AppLog);
				}

				return arguments.ToString().Trim();
			}
		}

		internal LMGridPooler(LISpMiner lispMiner, ODBC.ConnectionString connectionString, string lmPath, string gridPath)
			: base()
		{
			this.LISpMiner = lispMiner;
			this.LMPath = lmPath ?? this.LISpMiner.LMPath;
			this.OdbcConnectionString = connectionString.Value;

			this.GridBinariesPath = gridPath;

			this.ApplicationName = "LMGridPooler.exe";
			this.AppLog = String.Format("{0}-{1}.dat", "_AppLog_LMGridPooler", Guid.NewGuid());
			this.CancelAll = false;
			// this.TimeOut = 10;

			InitializePCGrid();
		}

		private void InitializePCGrid()
		{
			DirectoryInfo mainDirectory;
			string mainDirectoryPath = Path.GetFullPath(string.Format("{0}\\{1}", this.LMPath, "PCGrid"));

			if (!Directory.Exists(mainDirectoryPath))
			{
				mainDirectory = Directory.CreateDirectory(mainDirectoryPath);

				// Input
				mainDirectory.CreateSubdirectory("Input");

				// Output
				mainDirectory.CreateSubdirectory("Output");

				// Results
				mainDirectory.CreateSubdirectory("Results");

				// Temp
				mainDirectory.CreateSubdirectory("Temp");

				// SewebarConnect.grid.settings
				// SewebarConnect.jks
				GenereatePCGridSettings(mainDirectory);
			}
		}

		/// <summary>
		/// TODO: make correct values and allow to set them via settings.
		/// </summary>
		/// <param name="mainDirectory"></param>
		private void GenereatePCGridSettings(DirectoryInfo mainDirectory)
		{
			string filename = string.Format("{0}\\{1}", mainDirectory.FullName, "SewebarConnect.grid.settings");

			using (var filestream = new FileStream(filename, FileMode.Create))
			{
				using (var writer = new StreamWriter(filestream))
				{
					writer.WriteLine("#");
					writer.WriteLine("# Grid Client config file");
					writer.WriteLine("#");
					writer.WriteLine("");
					writer.WriteLine("# keystore filename");
					writer.WriteLine(string.Format("keystore={0}", this.InitializeKeyStore(mainDirectory, "SewebarConnect.jks")));
					writer.WriteLine("");
					writer.WriteLine("# key alias in the keystore");
					writer.WriteLine("alias=*****");
					writer.WriteLine("");
					writer.WriteLine("# keystore & key password");
					writer.WriteLine("# It is HIGHLY recommended not to write password here, but instead");
					writer.WriteLine("# use the password dialog below!");
					writer.WriteLine("password=*****");
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
					writer.WriteLine("hostname=techila.vse.cz");
					writer.WriteLine("");
					writer.WriteLine("# server port");
					writer.WriteLine("port=25001");
					writer.WriteLine("");
					writer.WriteLine("# temporary directory, use absolute path here, especially with Matlab");
					writer.WriteLine(string.Format("tempdir={0}\\Temp", mainDirectory.FullName));
					writer.WriteLine("");
					writer.WriteLine("#");
					writer.WriteLine("# Error file, if defined project errors are appended to the given file.");
					writer.WriteLine("#");
					writer.WriteLine(string.Format("#errorfile={0}\\Temp\\errorfeed", mainDirectory.FullName));
					writer.WriteLine("");
					writer.WriteLine("# Error directory, projects errors are appended to one file per project");
					writer.WriteLine("# in this directory");
					writer.WriteLine(string.Format("errordir={0}\\Temp\\Error", mainDirectory.FullName));
					writer.WriteLine("");
					writer.WriteLine("# if true the project errors are printed to STDERR (console)");
					writer.WriteLine("#stderr=true");
					writer.WriteLine("");
					writer.WriteLine("# file where all stdoutputs from the clients are fed.");
					writer.WriteLine("#stdoutfile=F:\\Guha\\PCGrid\\temp\\gridout.log");
					writer.WriteLine("");
					writer.WriteLine("# directory where stdoutputs from the clients are fed. Each project");
					writer.WriteLine("# will have a subdirectory created.");
					writer.WriteLine(string.Format("stdoutdir={0}\\Output", mainDirectory.FullName));
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
					writer.WriteLine(string.Format("logfile={0}\\Temp\\Log\\grid.log", mainDirectory.FullName));
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

		/// <summary>
		/// TODO: is it nesseccary to copy keystore. Is it OK to keep only one original?
		/// </summary>
		/// <param name="mainDirectory"> </param>
		/// <param name="keystore"></param>
		/// <returns></returns>
		private string InitializeKeyStore(DirectoryInfo mainDirectory, string keystore)
		{
			var origin = Path.GetFullPath(string.Format("{0}\\{1}", this.KeyStorePath, keystore));
			var result = Path.GetFullPath(string.Format("{0}\\{1}", mainDirectory.FullName, keystore));

			if(File.Exists(origin))
			{
				File.Copy(origin, result);

				return result;
			}

			throw new Exception(string.Format("Keystore {0} does not exist.", origin));
		}
	}  
}
