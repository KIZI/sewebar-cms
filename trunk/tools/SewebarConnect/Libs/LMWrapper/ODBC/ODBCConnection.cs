using System;
using System.Collections.Specialized;

namespace LMWrapper.ODBC
{
	public abstract class OdbcConnection
	{
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
			
		}
	}
}
