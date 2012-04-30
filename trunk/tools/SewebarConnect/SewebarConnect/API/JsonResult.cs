using System;
using System.IO;
using System.Web;
using System.Web.Mvc;
using Newtonsoft.Json;

namespace SewebarConnect.API
{
	public class JsonResult : System.Web.Mvc.JsonResult
	{
		public override void ExecuteResult(ControllerContext context)
		{
			if (context == null)
			{
				throw new ArgumentNullException("context");
			}
			if (JsonRequestBehavior == JsonRequestBehavior.DenyGet &&
				String.Equals(context.HttpContext.Request.HttpMethod, "GET", StringComparison.OrdinalIgnoreCase))
			{
				throw new InvalidOperationException("Get is not allowed");
			}

			HttpResponseBase response = context.HttpContext.Response;

			if (!String.IsNullOrEmpty(ContentType))
			{
				response.ContentType = ContentType;
			}
			else
			{
				response.ContentType = "application/json";
			}
			if (ContentEncoding != null)
			{
				response.ContentEncoding = ContentEncoding;
			}
			if (Data != null)
			{
				// AFAIK u can't do this, If you have a data contract class which u gonna serialize u need to apply the DataContract to all base classes to do this
				// Use the DataContractJsonSerializer instead of the JavaScriptSerializer 
				//DataContractJsonSerializer serializer = new DataContractJsonSerializer(Data.GetType());
				//serializer.WriteObject(response.OutputStream, Data);

				using (var stream = new StreamWriter(response.OutputStream))
				{
					var obj = JsonConvert.SerializeObject(
						this.Data,
						Formatting.None,
						new JsonSerializerSettings { NullValueHandling = NullValueHandling.Ignore });

					stream.Write(obj);
				}
			}
		}
	}
}
