using System;
using System.Data.Odbc;
using System.IO;

namespace LMWrapper.ODBC
{
	public class AccessConnection : OdbcConnection, IMetabase
	{
		#region Properties

		public string Path { get; protected set; }

		public override string ConnectionString
		{
			get
			{
				return String.Format("DSN={0}", this.DSN);
			}
		}

		#endregion

		public AccessConnection(string file, string dsn) : base(dsn)
		{
			this.Path = file;

			if (!ODBCManagerRegistry.DSNExists(this.DSN))
			{
				ODBCManagerRegistry.CreateDSN(this.DSN, "Auto created DSN for LISpMiner", "Microsoft Access Driver (*.mdb)", this.Path);
			}
		}

		public AccessConnection(string file, string fromFile, string dsn)
			: this(file, dsn)
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
                string sql = String.Format("UPDATE tpParamsDB SET strValue='{0}' WHERE Name='DSN'", database.DSN);

                using (var connection = new System.Data.Odbc.OdbcConnection(this.ConnectionString))
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

			if (ODBCManagerRegistry.DSNExists(this.DSN))
			{
				ODBCManagerRegistry.RemoveDSN(this.DSN);
			}
		}
	}
}
