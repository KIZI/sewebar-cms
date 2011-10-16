using System;
using System.Data.Odbc;
using System.IO;
using LMWrapper.ODBC;

namespace LMWrapper.LISpMiner
{
	public class LISpMiner : IDisposable
	{
		private LMSwbImporter _importer;
		private LMSwbExporter _exporter;
		private Task4ftGen _task4FtGen;

		public string Id { get; protected set; }

		public ODBCConnection Database { get; protected set; }

		public ODBCConnection Metabase { get; protected set; }

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
					this._exporter = new LMSwbExporter()
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

		static public void CopyFolder(string sourceFolder, string destFolder)
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

		public LISpMiner(Environment environment, string id, ODBCConnection database)
		{
			this.Id = id;
			this.LMPath = Path.Combine(environment.LMPoolPath, String.Format("{0}_{1}", "LISpMiner", this.Id));
			CopyFolder(environment.LMPath, this.LMPath);

			var metabase = new AccessConnection(String.Format(@"{0}\LM_Barbora_{1}.mdb", this.LMPath, id),
												String.Format(@"{0}\LM Barbora.mdb", environment.DataPath)) { DSN = String.Format("LM{0}", id) };

			this.Init(environment, id, database, metabase);
		}

		public LISpMiner(Environment environment, string id, ODBCConnection database, ODBCConnection metabase)
		{
			this.Id = id;
			this.LMPath = Path.Combine(environment.LMPoolPath, String.Format("{0}_{1}", "LISpMiner", this.Id));
			CopyFolder(environment.LMPath, this.LMPath);

			this.Init(environment, id, database, metabase);
		}

		protected void Init(Environment environment, string id, ODBCConnection database, ODBCConnection metabase)
		{
			this.SetDatabaseDsnToMetabase(metabase, database);
		}

		protected void SetDatabaseDsnToMetabase(ODBCConnection metabase, ODBCConnection database)
		{
			string sql = String.Format("UPDATE tpParamsDB SET strValue='{0}' WHERE Name='DSN'", database.DSN);

			using (var connection = new OdbcConnection(metabase.ConnectionString))
			{
				connection.Open();

				var command = new OdbcCommand(sql, connection);
				command.ExecuteNonQuery();
			}
		}

		public void Dispose()
		{
			this.Metabase.Dispose();
			this.Database.Dispose();

			Directory.Delete(this.LMPath, true);
		}
	}
}
