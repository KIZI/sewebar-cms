using System;
using System.Data.Odbc;
using System.IO;
using LMWrapper.ODBC;
using OdbcConnection = LMWrapper.ODBC.OdbcConnection;

namespace LMWrapper.LISpMiner
{
	public class LISpMiner : IDisposable
	{
		#region Statics

		protected static void CopyFolder(string sourceFolder, string destFolder)
		{
			if (!Directory.Exists(destFolder))
			{
				Directory.CreateDirectory(destFolder);
			}

			foreach (string folder in Directory.GetDirectories(sourceFolder))
			{
				string name = Path.GetFileName(folder);

				if (name == null) continue;

				string dest = Path.Combine(destFolder, name);
				CopyFolder(folder, dest);
			}

			foreach (string file in Directory.GetFiles(sourceFolder))
			{
				string name = Path.GetFileName(file);

				if (name == null) continue;

				string dest = Path.Combine(destFolder, name);
				File.Copy(file, dest, true);
			}
		}

		#endregion

		private LMSwbImporter _importer;
		private LMSwbExporter _exporter;
		private Task4ftGen _task4FtGen;
		private LMTaskPooler _lmTaskPooler;

		#region Properties

		public string Id { get; protected set; }

		public OdbcConnection Database { get; protected set; }

		public AccessConnection Metabase { get; protected set; }

		public string LMPath { get; set; }

		public ExecutableStatus Status
		{
			get
			{
				if (this.Importer.Status == ExecutableStatus.Ready
					&& this.Exporter.Status == ExecutableStatus.Ready
					&& this.Task4FtGen.Status == ExecutableStatus.Ready)
					return ExecutableStatus.Ready;

				return ExecutableStatus.Running;
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

		public Task4ftGen Task4FtGen
		{
			get
			{
				if(this._task4FtGen == null)
				{
					this._task4FtGen = new Task4ftGen
					{
						LMPath = this.LMPath,
						Dsn = this.Metabase.DSN,
						LISpMiner = this
					};
				}

				return this._task4FtGen;
			}

			set { this._task4FtGen = value; }
		}

		public LMTaskPooler LMTaskPooler
		{
			get
			{
				if (this._lmTaskPooler == null)
				{
					this._lmTaskPooler = new LMTaskPooler()
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

		protected Environment Environment { get; set; }

		#endregion

		protected LISpMiner(Environment environment, string id)
		{
			this.Id = id;
			this.Environment = environment;
			this.LMPath = Path.Combine(environment.LMPoolPath, String.Format("{0}_{1}", "LISpMiner", this.Id));

			CopyFolder(environment.LMPath, this.LMPath);
		}

		/// <summary>
		/// Creates LISpMiner with Access DB from given file.
		/// </summary>
		/// <param name="environment">Environment settings</param>
		/// <param name="id">Desired ID.</param>
		/// <param name="databasePrototypeFile">Original database.</param>
		public LISpMiner(Environment environment, string id, string databasePrototypeFile)
			: this(environment, id)
		{
			string databaseFile;
			string databaseDSN;

			this.GetDatabaseNames(databasePrototypeFile, out databaseFile, out databaseDSN);

			this.Database = new AccessConnection(databaseFile, databasePrototypeFile, databaseDSN);

			this.CreateMetabase();
		}

		/// <summary>
		/// Creates LISpMiner with given database. Metabase is created as Access DB.
		/// </summary>
		/// <param name="environment">Environment settings</param>
		/// <param name="id">Desired ID.</param>
		/// <param name="database">Original database.</param>
		public LISpMiner(Environment environment, string id, OdbcConnection database)
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

			this.CreateMetabase();
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
			string metabasePrototypeFile;
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


			this.GetMetabaseNames(out metabaseFile, out metabasePrototypeFile, out metabaseDSN);
			this.Metabase = new AccessConnection(metabaseFile, string.Empty, metabaseDSN);
		}

		protected void CreateMetabase()
		{
			string metabaseFile;
			string metabasePrototypeFile;
			string metabaseDSN;

			this.GetMetabaseNames(out metabaseFile, out metabasePrototypeFile, out metabaseDSN);

			this.Metabase = new AccessConnection(metabaseFile, metabasePrototypeFile, metabaseDSN);

			this.Metabase.SetDatabaseDsnToMetabase(this.Database);
		}

		protected void GetMetabaseNames(out string file, out string protofile, out string dsn)
		{
			//TODO: make default connection configurable
			file = String.Format(@"{0}\LM-metabase-{1}.mdb", this.LMPath, this.Id);
			protofile = String.Format(@"{0}\LM Barbora.mdb", Environment.DataPath);
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
