using System;
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
		public string Dsn { get; protected set; }
		public string MetabaseDsn { get; protected set; }
		protected string MetabasePath { get; set; }
		public string LMPath { get; set; }

		public LMSwbImporter Importer
		{
			get
			{
				if (this._importer == null)
				{
					this._importer = new LMSwbImporter
					{
						LMPath = this.LMPath,
						Dsn = this.MetabaseDsn,
						Quiet = true
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
						Dsn = this.MetabaseDsn,
						NoProgress = true,
						Quiet = true
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
						Dsn = this.MetabaseDsn,
						Quiet = true,
						NoProgress = true
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
				string dest = Path.Combine(destFolder, name);
				CopyFolder(folder, dest);
			}

			foreach (string file in Directory.GetFiles(sourceFolder))
			{
				string name = Path.GetFileName(file);
				string dest = Path.Combine(destFolder, name);
				File.Copy(file, dest);
			}
		}

		public LISpMiner(Environment environment, string id)
		{
			this.Id = id;
			this.LMPath = Path.Combine(environment.LMPoolPath, String.Format("{0}_{1}", "LISpMiner", this.Id));
			this.MetabaseDsn = String.Format("LM{0}", this.Id);
			this.MetabasePath = String.Format(@"{0}\LM_Barbora_{1}.mdb", this.LMPath, this.Id);

			var metabaseOriginal = String.Format(@"{0}\LM Barbora.mdb", environment.DataPath);

			CopyFolder(environment.LMPath, this.LMPath);

			if (!ODBCManagerRegistry.DSNExists(this.MetabaseDsn) && !File.Exists(this.MetabasePath))
			{
				File.Copy(metabaseOriginal, this.MetabasePath, true);

				ODBCManagerRegistry.CreateDSN(this.MetabaseDsn, "", "Microsoft Access Driver (*.mdb)", this.MetabasePath);
			}
		}

		public void Dispose()
		{
			//if (ODBCManagerRegistry.DSNExists(this.MetabaseDsn))
			//{
			ODBCManagerRegistry.RemoveDSN(this.MetabaseDsn);
			//}

			File.Delete(this.MetabasePath);
			Directory.Delete(this.LMPath, true);
		}
	}
}
