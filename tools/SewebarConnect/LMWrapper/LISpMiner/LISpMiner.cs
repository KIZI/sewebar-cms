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

		#region Properties

		public string Id { get; protected set; }

		public DateTime Created { get; protected set; }

		public OdbcConnection Database { get; protected set; }

		public AccessConnection Metabase { get; protected set; }

		public string LMPath { get; set; }

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
					this._importer = new LMSwbImporter
					{
						LMPath = this.LMPath,
						Dsn = this.Metabase.DSN,
						LISpMiner = this
					};
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
					this._exporter = new LMSwbExporter
					{
						LMPath = this.LMPath,
						Dsn = this.Metabase.DSN,
						LISpMiner = this
					};
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
					this._lmTaskPooler = new LMTaskPooler
					{
						LMPath = this.LMPath,
						Dsn = this.Metabase.DSN,
						LISpMiner = this
					};
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
					this._lmProcPooler = new LMProcPooler
					{
						LMPath = this.LMPath,
						Dsn = this.Metabase.DSN,
						LISpMiner = this
					};
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
					this._lmGridPooler = new LMGridPooler(this, this.Metabase.DSN, this.LMPath, this.Environment.PCGridPath);
				}

				return this._lmGridPooler;
			}

			set { this._lmGridPooler = value; }
		}

		protected Environment Environment { get; set; }

		#endregion

		protected LISpMiner(Environment environment, string id)
		{
			this.Id = id;
			this.Environment = environment;
			this.LMPath = Path.Combine(environment.LMPoolPath, String.Format("{0}_{1}", "LISpMiner", this.Id));

			DirectoryUtil.Copy(environment.LMPath, this.LMPath);

			this.Created = DateTime.Now;
		}

		/// <summary>
		/// Creates LISpMiner with Access DB from given file.
		/// </summary>
		/// <param name="environment">Environment settings</param>
		/// <param name="id">Desired ID.</param>
		/// <param name="databasePrototypeFile">Original database.</param>
		/// <param name="metabasePrototypeFile">Name of metabase file to use. Must exist in data folder.</param>
		public LISpMiner(Environment environment, string id, string databasePrototypeFile, string metabasePrototypeFile)
			: this(environment, id)
		{
			string databaseFile;
			string databaseDSN;

			this.GetDatabaseNames(databasePrototypeFile, out databaseFile, out databaseDSN);

			this.Database = new AccessConnection(databaseFile, databasePrototypeFile, databaseDSN);

			this.CreateMetabase(metabasePrototypeFile);
		}

		/// <summary>
		/// Creates LISpMiner with given database. Metabase is created as Access DB.
		/// </summary>
		/// <param name="environment">Environment settings</param>
		/// <param name="id">Desired ID.</param>
		/// <param name="database">Original database.</param>
		/// <param name="metabasePrototypeFile">Name of metabase file to use. Must exist in data folder.</param>
		public LISpMiner(Environment environment, string id, OdbcConnection database, string metabasePrototypeFile)
			: this(environment, id)
		{
			if (database != null)
			{
				this.Database = database;
			}
			else
			{
				throw new NullReferenceException("Database can't be null.");
			}

			this.CreateMetabase(metabasePrototypeFile);
		}

		/// <summary>
		/// Creates instance of LISpMiner for existing location.
		/// </summary>
		/// <param name="lmpath">Path to LM folder.</param>
		public LISpMiner(DirectoryInfo lmpath, Environment env)
		{
			if (!lmpath.Exists)
			{
				throw new Exception(String.Format("LISpMiner does not exist at location {0}", lmpath.FullName));
			}

			this.Environment = env;
			this.Id = lmpath.Name.Substring("LISpMiner_".Length);
			this.LMPath = lmpath.FullName;

			string metabaseFile;
			string devNull = string.Empty;
			string metabaseDSN;
			string databaseFile;
			string databaseDSN;

			this.GetDatabaseNames(string.Empty, out databaseFile, out databaseDSN);

			if (File.Exists(databaseFile))
			{
				this.Database = new AccessConnection(databaseFile, string.Empty, databaseDSN);
			}
			else
			{
				this.Database = new MySQLConnection(databaseDSN);
			}


			this.GetMetabaseNames(out metabaseFile, ref devNull, out metabaseDSN);
			this.Metabase = new AccessConnection(metabaseFile, string.Empty, metabaseDSN);

			this.Created = lmpath.CreationTime;
		}

		protected void CreateMetabase(string metabasePrototypeFile)
		{
			string metabaseFile;
			string metabaseDSN;

			this.GetMetabaseNames(out metabaseFile, ref metabasePrototypeFile, out metabaseDSN);

			this.Metabase = new AccessConnection(metabaseFile, metabasePrototypeFile, metabaseDSN);

			this.Metabase.SetDatabaseDsnToMetabase(this.Database);
		}

		protected void GetMetabaseNames(out string file, ref string protofile, out string dsn)
		{
			if (string.IsNullOrEmpty(protofile))
			{
				protofile = "LMEmpty.mdb";
			}

			//TODO: make default connection configurable
			file = String.Format(@"{0}\LM-metabase-{1}.mdb", this.LMPath, this.Id);
			protofile = String.Format(@"{0}\{1}", Environment.DataPath, protofile);
			dsn = String.Format("LMM-{0}", this.Id);
		}

		protected void GetDatabaseNames(string databasePrototypeFile, out string file, out string dsn)
		{
			if (String.IsNullOrEmpty(databasePrototypeFile))
			{
				// because it is default to create with
				databasePrototypeFile = "Barbora.mdb";
			}
			
			var databaseName = Path.GetFileNameWithoutExtension(databasePrototypeFile);
			file = String.Format(@"{0}\LM-{2}-{1}.mdb", this.LMPath, this.Id, databaseName);
			dsn = String.Format("LM{0}", this.Id);
		}

		protected Version GetVersion()
		{
			var versionPath = String.Format("{0}/version.xml", this.LMPath);

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

			Directory.Delete(this.LMPath, true);
		}
	}
}
