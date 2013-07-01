using System;
using System.Linq;
using System.Web;
using System.Xml.Linq;
using LMWrapper.ODBC;

namespace SewebarConnect.API.Requests.Application
{
	public class RegistrationRequest : Request
	{
		private XDocument _buffer;
		private DbConnection _dbConnection;
		private DbConnection _dbMetabase;

		public static DbConnection GetDbConnection(string which, XDocument requestBody)
		{
			OdbcDrivers type;
			var conn = (from element in requestBody
							.Elements("RegistrationRequest")
							.Elements(which)
						select element).ToList();

			if (conn.Count == 0)
			{
				if (which == "Metabase")
				{
					return null;
				}

				throw new Exception(String.Format("Database was not correctly defined ({0}).", which));
			}

			var connType = (from element in conn
							select (string)element.Attribute("type")).SingleOrDefault();

			if (connType != null && !connType.EndsWith("Connection"))
			{
				connType = connType + "Connection";
			}

			if (!OdbcDrivers.TryParse(connType, true, out type))
			{
				throw new Exception(String.Format("Database was not correctly defined (type of {0}).", which));
			}

			switch (type)
			{
				case OdbcDrivers.AccessConnection:
					return (from element in conn
							select
								new DbConnection
								{
									Type = type,
									Filename = (string)element.Element("File")
								}).SingleOrDefault();
				case OdbcDrivers.MySqlConnection:
					return (from element in conn
							select
								new DbConnection
								{
									Type = type,
									Server = (string)element.Element("Server"),
									Database = (string)element.Element("Database"),
									Username = (string)element.Element("Username"),
									Password = (string)element.Element("Password")
								}).SingleOrDefault();
			}

			throw new Exception(String.Format("Database was not correctly defined ({0}).", which));
		}

		public DbConnection Metabase
		{
			get { return this._dbMetabase ?? (this._dbMetabase = GetDbConnection("Metabase", this.GetRequest())); }
		}

		public DbConnection DbConnection
		{
			get { return this._dbConnection ?? (this._dbConnection = GetDbConnection("Connection", this.GetRequest())); }
		}

		public RegistrationRequest()
			: base(new HttpContextWrapper(System.Web.HttpContext.Current))
		{
		}

		private XDocument GetRequest()
		{
			return _buffer ?? (_buffer = XDocument.Load(this.HttpContext.Request.InputStream));
		}
	}
}