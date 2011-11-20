using System;
using System.Web;
using SewebarWeb.API;

namespace SewebarWeb
{
	public class Import : HttpHandlerSession
	{
		private API.ImportResponse Response { get; set; }
	
		private API.ImportRequest Request { get; set; }
	
		public override void ProcessRequest(HttpContext context)
		{
			base.ProcessRequest(context);
			
			Request = new API.ImportRequest(this.Miner, context);
			
			Response = new API.ImportResponse(context) {
			                                      Id = context.Session.SessionID
			                                  };

			if (this.Miner != null && Request.DataDictionary != null)
			{
				var importer = this.Miner.Importer;
				importer.Input = Request.DataDictionaryPath;
				importer.Execute();

				Response.Message = String.Format("Imported {0} to {1}", importer.Input, importer.Dsn);
				Response.Status = Status.success;
			}
			
			Response.Write();
		}
	}
}