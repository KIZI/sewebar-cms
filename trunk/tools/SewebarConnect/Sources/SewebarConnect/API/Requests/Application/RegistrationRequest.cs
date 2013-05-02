using System;
using System.Web;
using LMWrapper.ODBC;

namespace SewebarConnect.API.Requests.Application
{
	public class RegistrationRequest : Request
	{
		private DbConnection _dbConnection;

		public string Metabase
		{
			get
			{
				string file = this.HttpContext.Request["metabase"];
				return file ?? string.Empty;
			}
		}

		public DbConnection DbConnection
		{
			get
			{
				if (this._dbConnection == null)
				{
					var parameters = this.HttpContext.Request.Params;
					OdbcDrivers type;

					if (!OdbcDrivers.TryParse(parameters["type"], true, out type))
					{
						throw new Exception("Database was not correctly defined (type).");
					}

					this._dbConnection = new DbConnection
					                     	{
												Type = type,
												Server = parameters["server"],
												Database = parameters["database"],
												Password = parameters["password"],
												Username = parameters["username"]
					                     	};
				}
				
				return this._dbConnection;
			}
		}

		public RegistrationRequest(HttpContextBase context)
			: base(null, context)
		{
		}

		public RegistrationRequest(HttpContext context)
			: this(new HttpContextWrapper(context))
		{
		}
	}
}