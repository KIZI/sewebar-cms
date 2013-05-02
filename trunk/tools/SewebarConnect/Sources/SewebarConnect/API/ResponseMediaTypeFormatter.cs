using System;
using System.IO;
using System.Net.Http.Formatting;
using System.Net.Http.Headers;
using System.Threading.Tasks;

namespace SewebarConnect.API
{
	public class ResponseMediaTypeFormatter : MediaTypeFormatter
	{
		private readonly string appXml = "application/xml";
		private readonly string textPlain = "text/plain";
		private readonly string textXml = "text/xml";

		public ResponseMediaTypeFormatter()
		{
			SupportedMediaTypes.Add(new MediaTypeHeaderValue(appXml));
			SupportedMediaTypes.Add(new MediaTypeHeaderValue(textPlain));
			SupportedMediaTypes.Add(new MediaTypeHeaderValue(textXml));
		}

		public override bool CanReadType(Type type)
		{
			return type.IsSubclassOf(typeof(Response)) || type == typeof(Response);
		}

		public override bool CanWriteType(Type type)
		{
			return type.IsSubclassOf(typeof(Response)) || type == typeof(Response);
		}

		public override Task WriteToStreamAsync(Type type, object value, Stream writeStream, System.Net.Http.HttpContent content, System.Net.TransportContext transportContext)
		{
			return Task.Factory.StartNew(() => WriteResponse(value as Response, writeStream));
		}

		private void WriteResponse(Response response, Stream stream)
		{
			if (response != null)
			{
				var fileResponse = response as IFileResponse;

				using (var w = new StreamWriter(stream))
				{
					if (fileResponse != null)
					{
						File.OpenRead(fileResponse.GetFile()).CopyTo(stream);
					}
					else
					{
						w.Write(response.Write());    
					}
					
					w.Flush();
					w.Close();
				}
			}
		}
	}
}
