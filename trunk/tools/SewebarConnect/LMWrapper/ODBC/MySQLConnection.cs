using System;

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

		public MySQLConnection(string dsn)
			: base(dsn)
		{
			if (!ODBCManagerRegistry.DSNExists(this.DSN))
			{
				throw new Exception(
					String.Format(
						"DSN with given name does not exists ({0}), therefore it is not possible to create connection without definition",
						this.DSN));
			}
		}

		public MySQLConnection(string dsn, string server, string db, string username, string pass)
			: base(dsn)
		{
			this.ServerAddress = server;
			this.Database = db;
			this.Username = username;
			this.Password = pass;

			if (!ODBCManagerRegistry.DSNExists(this.DSN))
			{
				ODBCManagerRegistry.CreateDSN(this.DSN, "Auto created DSN for LISpMiner", this.ServerAddress, this.Database, this.Username, this.Password);
			}
		}

		public override string ConnectionString
		{
			get { return string.Format("Server={0};Database={1};Uid={2};Pwd={3};", ServerAddress, Database, Username, Password); }
		}
	}
}
