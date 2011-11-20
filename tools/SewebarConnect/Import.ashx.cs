using System;
using System.IO;
using System.Web;

namespace SewebarWeb
{
	public class Import : SessionBase
	{
		public override void ProcessRequest(HttpContext context)
		{
			base.ProcessRequest(context);
			
			var miner = this.Miner;
			var content = context.Request["content"];
			var dataFolder = String.Format("{1}/xml/{0}", miner != null ? miner.Id : String.Empty, AppDomain.CurrentDomain.GetData("DataDirectory"));
			var response = new API.ImportResponse {
				Id = context.Session.SessionID
			};

			if (miner != null && content != null)
			{
				if (!Directory.Exists(dataFolder)) Directory.CreateDirectory(dataFolder);

				var input = String.Format(@"{0}/DataDictionary_{1:yyyyMMdd-Hmmss}.xml", dataFolder, DateTime.Now);

				using (var file = File.CreateText(input))
				{
					file.Write(content);
					file.Close();
				}

				var importer = miner.Importer;
				importer.Input = input;
				importer.Execute();

				response.Message = String.Format("Imported {0} to {1}", importer.Input, importer.Dsn);
				response.Status = Status.success;
			}
			
			context.Response.ContentType = "text/xml";

			response.ToXml().Save(context.Response.OutputStream);
		}
	}
}