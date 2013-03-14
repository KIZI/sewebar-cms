using System;
using System.IO;
using System.Text;

namespace LMWrapper.ODBC
{
	public class MySQLConnection : OdbcConnection
	{
		#region Properties

		public string Database { get; set; }

		public string Password { get; set; }

		public string Username { get; set; }

		public string ServerAddress { get; set; }

		#endregion

		internal MySQLConnection(string dsnFile)
			: base(dsnFile)
		{
			if (!File.Exists(this.DSNFile))
			{
				throw new Exception(
					String.Format(
						"DSN File \"{0}\" does not exists, therefore it is not possible to create connection without definition",
						this.DSNFile));
			}
		}

		internal MySQLConnection(string dsnFile, string server, string db, string username, string pass)
			: this(
				dsnFile,
				new DbConnection
					{
						Type = OdbcDrivers.MySqlConnection,
						Server = server,
						Database = db,
						Username = username,
						Password = pass
					})
		{

		}

		internal MySQLConnection(string databaseDSNFile, DbConnection connection)
			: base(databaseDSNFile)
		{
			if (connection == null)
			{
				throw new Exception("Connection definition cannot be null!");
			}

			this.ServerAddress = connection.Server;
			this.Database = connection.Database;
			this.Username = connection.Username;
			this.Password = connection.Password;

			if (!File.Exists(this.DSNFile))
			{
				this.CreateDSNFile();
			}
		}

		private void CreateDSNFile()
		{
			using (var writer = new StreamWriter(this.DSNFile, false, Encoding.ASCII))
			{
				writer.WriteLine("[ODBC]");
				writer.WriteLine("DRIVER=MySQL ODBC 5.1 Driver");
				writer.WriteLine(string.Format("SERVER={0}", this.ServerAddress));
				writer.WriteLine(string.Format("DATABASE={0}", this.Database));
				writer.WriteLine(string.Format("PORT={0}", 3306));
				writer.WriteLine(string.Format("OPTION={0}", 3));
				writer.WriteLine(string.Format("UID={0}", this.Username));
				writer.WriteLine(string.Format("PWD={0}", this.Password));
			}
		}
	}
}
