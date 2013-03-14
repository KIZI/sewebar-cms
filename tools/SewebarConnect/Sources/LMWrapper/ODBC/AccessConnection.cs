using System;
using System.Data.Odbc;
using System.IO;
using System.Text;

namespace LMWrapper.ODBC
{
	public class AccessConnection : OdbcConnection, IMetabase
	{
		private const string Driver = "Microsoft Access Driver (*.mdb)";

		#region Properties

		public string Path { get; protected set; }

		#endregion

		internal AccessConnection(string dsnFile, string file) : base(dsnFile)
		{
			this.Path = System.IO.Path.GetFullPath(file);

			if (!File.Exists(this.DSNFile))
			{
				this.CreateDSNFile();
			}
		}

		internal AccessConnection(string dsnFile, string file, string fromFile)
			: this(dsnFile, file)
		{
			if (!File.Exists(this.Path) && !String.IsNullOrEmpty(fromFile) && File.Exists(fromFile))
			{
				File.Copy(fromFile, this.Path, true);
			}
		}

		public virtual void SetDatabaseDsnToMetabase(OdbcConnection database)
		{
			try
			{
				string sql = String.Format("UPDATE tpParamsDB SET strValue='{0}' WHERE Name='DSN'", database.ConnectionString.Value);

				using (var connection = new System.Data.Odbc.OdbcConnection(this.ConnectionString.Value))
				{
					connection.Open();

					var command = new OdbcCommand(sql, connection);
					command.ExecuteNonQuery();
				}
			}
			catch(Exception exception)
			{
				throw new Exception(String.Format("Could not set database DSN to metabase ({0}).", exception.Message), exception);
			}
		}

		public override void Destroy()
		{
			File.Delete(this.Path);

			if (File.Exists(this.DSNFile))
			{
				File.Delete(this.DSNFile);
			}
		}

		private void CreateDSNFile()
		{
			using (var writer = new StreamWriter(this.DSNFile, false, Encoding.ASCII))
			{
				writer.WriteLine("[ODBC]");
				writer.WriteLine(string.Format("DRIVER={0}", Driver));
				writer.WriteLine(string.Format("DefaultDir=\"{0}\"", System.IO.Path.GetDirectoryName(this.Path)));
				writer.WriteLine(string.Format("DBQ=\"{0}\"", this.Path));
				writer.WriteLine(string.Format("ReadOnly={0}", 0));
				writer.WriteLine(string.Format("UserCommitSync={0}", "Yes"));
				writer.WriteLine(string.Format("Threads={0}", 3));
				writer.WriteLine(string.Format("SafeTransactions={0}", 0));
				writer.WriteLine(string.Format("PageTimeout={0}", 5));
				writer.WriteLine(string.Format("MaxScanRows={0}", 8));
				writer.WriteLine(string.Format("MaxBufferSize={0}", 512));
				writer.WriteLine(string.Format("ImplicitCommitSync={0}", "Yes"));
				writer.WriteLine("FIL=MS Access");
				writer.WriteLine(string.Format("DriverId={0}", 25));
				writer.WriteLine(string.Format("UID={0}", "admin"));
				//writer.WriteLine(string.Format("PWD={0}", "admin"));
			}
		}
	}
}
