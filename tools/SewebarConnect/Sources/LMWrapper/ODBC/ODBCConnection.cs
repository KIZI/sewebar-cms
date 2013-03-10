using System;
using System.Collections.Specialized;
using System.IO;

namespace LMWrapper.ODBC
{
	public abstract class OdbcConnection
	{
		public static OdbcConnection Create(Environment environment, string id, DbConnection connection)
		{
			var databaseDSN = String.Format("LM{0}", id);

			switch (connection.Type)
			{
				case OdbcDrivers.MySqlConnection:
					var databaseServer = connection.Server;
					var databaseDatabase = connection.Database;
					var databasePassword = connection.Password;
					var databaseUsername = connection.Username;

					return new MySQLConnection(databaseDSN, databaseServer, databaseDatabase, databaseUsername, databasePassword);
					break;
				case OdbcDrivers.AccessConnection:
				default:
					var folder = String.Format(@"{0}\Barbora", environment.DataPath);

					if (!Directory.Exists(folder))
					{
						Directory.CreateDirectory(folder);
					}

					var databaseFile = String.Format(@"{0}\Barbora_{1}.mdb", folder, id);
					var databasePrototypeFile = String.Format(@"{0}\Barbora.mdb", environment.DataPath);

					return new AccessConnection(databaseFile, databasePrototypeFile, databaseDSN);
					break;
			}
		}

		public static OdbcConnection Create(OdbcDrivers type, Environment environment, string id, NameValueCollection parameters)
		{
			var databaseDSN = String.Format("LM{0}", id);

			switch (type)
			{
				case OdbcDrivers.MySqlConnection:
					var databaseServer = parameters["server"];
					var databaseDatabase = parameters["database"];
					var databasePassword = parameters["password"];
					var databaseUsername = parameters["username"];

					return new MySQLConnection(databaseDSN, databaseServer, databaseDatabase, databaseUsername, databasePassword);
					break;
				case OdbcDrivers.AccessConnection:
				default:
					var databaseFile = String.Format(@"{0}\Barbora_{1}.mdb", environment, id);
					var databasePrototypeFile = String.Format(@"{0}\Barbora.mdb", environment.DataPath);

					return new AccessConnection(databaseFile, databasePrototypeFile, databaseDSN);
					break;   
			}
		}

		public string DSN { get; set; }

		public abstract string ConnectionString { get; }

		protected OdbcConnection(string dsn)
		{
			this.DSN = dsn;
		}

		public virtual void Init()
		{
			
		}

		public virtual void Destroy()
		{
			if (ODBCManagerRegistry.DSNExists(this.DSN))
			{
				ODBCManagerRegistry.RemoveDSN(this.DSN);
			}
		}
	}
}
