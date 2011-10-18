using System;
using System.Collections.Specialized;
using System.Data.Odbc;

namespace LMWrapper.ODBC
{
	public abstract class OdbcConnection
	{
        public static OdbcConnection Create(OdbcConnectionsEnum type, Environment environment, string id, NameValueCollection parameters)
        {
            var databaseDSN = String.Format("LM{0}", id);

            switch (type)
            {
                case OdbcConnectionsEnum.MySQLConnection:
                    var databaseServer = parameters["server"];
                    var databaseDatabase = parameters["database"];
                    var databasePassword = parameters["password"];
                    var databaseUsername = parameters["username"];

                    return new MySQLConnection(databaseDSN, databaseServer, databaseDatabase, databaseUsername, databasePassword);
                    break;
                case OdbcConnectionsEnum.AccessConnection:
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
