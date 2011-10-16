using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using LMWrapper;
using LMWrapper.LISpMiner;
using LMWrapper.ODBC;
using Newtonsoft.Json;

namespace SewebarWeb
{
	/// <summary>
	/// Summary description for Register
	/// </summary>
	public class Register : IHttpHandler
	{
		#region Properties

		public bool IsReusable
		{
			get
			{
				return false;
			}
		}

		#endregion

		public void ProcessRequest(HttpContext context)
		{
			var id = ShortGuid.NewGuid();
			var database = new AccessConnection(String.Format(@"{0}\Barbora_{1}.mdb", Global.Environment.LMPath, id),
			                                    String.Format(@"{0}\Barbora.mdb", Global.Environment.DataPath))
			               	{DSN = String.Format("LM{0}", id)};


			//var guid = Global.Environment.Register(ODBCConnection.Create());
			Global.Environment.Register(new LISpMiner(Global.Environment, id.ToString(), database));
			var result = new {Status = "success", Name = id.Value};

			context.Response.ContentType = "text/json";
			context.Response.Write(JsonConvert.SerializeObject(result));
		}
	}
}