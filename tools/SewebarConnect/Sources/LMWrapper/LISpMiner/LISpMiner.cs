using System;
using System.IO;
using LMWrapper.ODBC;
using LMWrapper.Utils;
using OdbcConnection = LMWrapper.ODBC.OdbcConnection;

namespace LMWrapper.LISpMiner
{
	public class LISpMiner : IDisposable
	{
		private LMSwbImporter _importer;
		private LMSwbExporter _exporter;
		private LMTaskPooler _lmTaskPooler;
		private LMProcPooler _lmProcPooler;
		private LMGridPooler _lmGridPooler;
		private Version _version;

		internal static string GetMinerPath(Environment environment, string minerId)
		{
			return Path.GetFullPath(Path.Combine(environment.LMPoolPath, String.Format("{0}_{1}", "LISpMiner", minerId)));
		}

		private static void CopyToPool(Environment environment, LISpMiner miner)
		{
			if (!miner.SharedPool)
			{
				DirectoryUtil.Copy(environment.LMPath, miner.LMPath);
			}
			else
			{
				Directory.CreateDirectory(miner.LMPrivatePath);
			}
		}

		#region Properties

		public string Id { get; protected set; }

		public DateTime Created { get; protected set; }

		public OdbcConnection Database { get; protected set; }

		public AccessConnection Metabase { get; protected set; }

		internal string LMPath
		{
			get
			{
				if (this.SharedPool)
				{
					return this.Environment.LMPath;
				}

				return this.LMPrivatePath;
			}
		}

		public string LMPrivatePath { get; private set; }

		public Version Version
		{
			get
			{
				if (this._version == null)
				{
					this._version = GetVersion();
				}

				return this._version;
			}
		}

		public LMSwbImporter Importer
		{
			get
			{
				if (this._importer == null)
				{
					this._importer = new LMSwbImporter(this, this.Metabase.ConnectionString, this.LMPath);
				}

				return this._importer;
			}

			set { this._importer = value; }
		}

		public LMSwbExporter Exporter
		{
			get
			{
				if (this._exporter == null)
				{
					this._exporter = new LMSwbExporter(this, this.Metabase.ConnectionString, this.LMPath);
				}

				return this._exporter;
			}

			set { this._exporter = value; }
		}

		public LMTaskPooler LMTaskPooler
		{
			get
			{
				if (this._lmTaskPooler == null)
				{
					this._lmTaskPooler = new LMTaskPooler(this, this.Metabase.ConnectionString, this.LMPath);
				}

				return this._lmTaskPooler;
			}

			set { this._lmTaskPooler = value; }
		}

		public LMProcPooler LMProcPooler
		{
			get
			{
				if (this._lmProcPooler == null)
				{
					this._lmProcPooler = new LMProcPooler(this, this.Metabase.ConnectionString, this.LMPath);
				}

				return this._lmProcPooler;
			}

			set { this._lmProcPooler = value; }
		}

		public LMGridPooler LMGridPooler
		{
			get
			{
				if (this._lmGridPooler == null)
				{
					this._lmGridPooler = new LMGridPooler(this, this.Metabase.ConnectionString, this.LMPath, this.Environment.PCGridPath);
				}

				return this._lmGridPooler;
			}

			set { this._lmGridPooler = value; }
		}

		public bool SharedPool { get; private set; }

		internal Environment Environment { get; set; }

		#endregion

		protected LISpMiner(Environment environment, string id, bool sharedPool)
		{
			this.Id = id;
			this.Environment = environment;
			this.LMPrivatePath = GetMinerPath(environment, this.Id);
			this.SharedPool = sharedPool;

			CopyToPool(this.Environment, this);

			this.Created = DateTime.Now;
		}

		/// <summary>
		/// Creates LISpMiner with given database. Only supported metabase is Access DB.
		/// </summary>
		/// <param name="environment">Environment settings.</param>
		/// <param name="id">Desired ID.</param>
		/// <param name="connection">Connection to original database.</param>
		/// <param name="metabaseConnection">Name of metabase file to use. Must exist in data folder.</param>
		public LISpMiner(Environment environment, string id, DbConnection connection, DbConnection metabaseConnection, bool sharedPool)
			: this(environment, id, sharedPool)
		{
			string databaseFile;
			string databaseDSNFile;

			string databasePrototypeFile = this.GetDatabaseNames(connection.Database, out databaseFile, out databaseDSNFile);

			if (connection.Type == OdbcDrivers.MySqlConnection)
			{
				this.Database = new MySQLConnection(databaseDSNFile, connection);
			}
			else
			{
				this.Database = new AccessConnection(databaseDSNFile, databaseFile, databasePrototypeFile);
			}

			if (this.Database == null)
			{
				throw new NullReferenceException("Database can't be null.");
			}

			this.CreateMetabase(metabaseConnection);
		}

		/// <summary>
		/// Creates instance of LISpMiner for existing location.
		/// </summary>
		/// <param name="lmpath">Path to LM folder.</param>
		/// <param name="env">Environment settings.</param>
		public LISpMiner(DirectoryInfo lmpath, Environment env)
		{
			if (!lmpath.Exists)
			{
				throw new Exception(String.Format("LISpMiner does not exist at location {0}", lmpath.FullName));
			}

			this.Environment = env;
			this.Id = lmpath.Name.Substring("LISpMiner_".Length);
			this.LMPrivatePath = lmpath.FullName;

			this.SharedPool = !File.Exists(Path.Combine(this.LMPrivatePath, "LMTaskPooler.exe"));

			string metabaseFile;
			string devNull = string.Empty;
			string metabaseDSNFile;
			string databaseFile;
			string databaseDSNFile;

			this.GetDatabaseNames(string.Empty, out databaseFile, out databaseDSNFile);

			if (File.Exists(databaseFile))
			{
				this.Database = new AccessConnection(databaseDSNFile, databaseFile, string.Empty);
			}
			else
			{
				this.Database = new MySQLConnection(databaseDSNFile);
			}


			this.GetMetabaseNames(out metabaseFile, ref devNull, out metabaseDSNFile);
			this.Metabase = new AccessConnection(metabaseDSNFile, metabaseFile, string.Empty);

			this.Created = lmpath.CreationTime;
		}

		protected void CreateMetabase(DbConnection metabaseConnection)
		{
			string metabaseFile;
			string dsnFile;
			string metabasePrototypeFile = metabaseConnection != null ? metabaseConnection.Filename : string.Empty;

			this.GetMetabaseNames(out metabaseFile, ref metabasePrototypeFile, out dsnFile);

			this.Metabase = new AccessConnection(dsnFile, metabaseFile, metabasePrototypeFile);

			this.Metabase.SetDatabaseDsnToMetabase(this.Database);
		}

		protected void GetMetabaseNames(out string file, ref string protofile, out string dsnFile)
		{
			if (string.IsNullOrEmpty(protofile))
			{
				protofile = String.Format(@"{0}\{1}", Environment.DataPath, "LMEmpty.mdb");
			}
			else
			{
				protofile = String.Format(@"{0}\{1}", Environment.DataPath, protofile);
			}

			file = String.Format(@"{0}\LM.MB.mdb", this.LMPrivatePath);
			dsnFile = String.Format(@"{0}\LM.MB.dsn", this.LMPrivatePath);
		}

		protected string GetDatabaseNames(string databasePrototypeFile, out string file, out string dsnFile)
		{
			// TODO: make default database connection mandatory
			const string defaultDatabase = "Barbora.mdb";
			string databasePrototypePath = Path.Combine(this.Environment.DataPath, databasePrototypeFile ?? defaultDatabase);

			if (String.IsNullOrEmpty(databasePrototypeFile) || Path.GetExtension(databasePrototypePath) != "mdb" || !File.Exists(databasePrototypePath))
			{
				// because it is default to create with
				databasePrototypePath = Path.Combine(this.Environment.DataPath, defaultDatabase);
			}
			
			var databaseName = Path.GetFileNameWithoutExtension(databasePrototypePath);
			file = String.Format(@"{0}\LM-{1}.mdb", this.LMPrivatePath, databaseName);
			dsnFile = String.Format(@"{0}\LM.dsn", this.LMPrivatePath);

			return databasePrototypePath;
		}

		protected Version GetVersion()
		{
			string versionPath;

			if (this.SharedPool)
			{
				versionPath = String.Format("{0}/version.xml", this.LMPath);
			}
			else
			{
				versionPath = String.Format("{0}/version.xml", this.LMPrivatePath);
			}

			if (!File.Exists(versionPath))
			{
				var exporter = this.Exporter;

				if (exporter.Status == ExecutableStatus.Ready)
				{
					exporter.Version = true;
					exporter.Output = versionPath;
					exporter.Template = String.Format(@"{0}\Sewebar\Template\{1}", exporter.LMPath, "LMVersion.Template.TXT");

					exporter.Execute();

					// Clean up
					exporter.Version = false;
					exporter.Output = String.Empty;
					exporter.Template = String.Empty;
				}
				else
				{
					throw new Exception("LM Exporter is occupied at this moment.");
				}
			}

			if (File.Exists(versionPath))
			{
				using (var reader = new StreamReader(versionPath))
				{
					return new Version(reader.ReadToEnd());
				}
			}

			throw new Exception("Version was not correctly exported");
		}

		public string GetTaskList()
		{
			var exporter = this.Exporter;
			var tasksFile = String.Format("{0}/LMTaskSurvey_{1:yyyyMMdd-Hmmss}.txt", exporter.LMPath, DateTime.Now);

			if (exporter.Status == ExecutableStatus.Ready)
			{
				exporter.Survey = true;
				exporter.Output = tasksFile;
				exporter.Template = String.Format(@"{0}\Sewebar\Template\{1}", exporter.LMPath, "LMSurvey.Task.Template.TXT");

				exporter.Execute();

				// Clean up
				exporter.Survey = false;
				exporter.Output = String.Empty;
				exporter.Template = String.Empty;

				using (var reader = new StreamReader(tasksFile))
				{
					return reader.ReadToEnd();
				}
			}

			throw new Exception("LM Exporter is occupied at this moment.");
		}

		public void Dispose()
		{
			if (this.Metabase != null)
			{
				this.Metabase.Destroy();
			}

			if (this.Database != null)
			{
				this.Database.Destroy();
			}

			Directory.Delete(this.LMPrivatePath, true);
		}
	}
}
