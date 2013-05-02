using System;
using System.IO;
using System.Web;
using SewebarConnect.Controllers;

namespace SewebarConnect.API.Requests.DataDictionary
{
	public class ImportRequest : Request
	{
		private string _dataDictionary;

		private string _dataDictionaryPath;

		public string DataDictionary
		{
			get { return _dataDictionary ?? (_dataDictionary = ReadDataDictionary()); }
		}

		public string DataDictionaryPath
		{
			get
			{
				if (String.IsNullOrEmpty(this._dataDictionaryPath))
				{
					this._dataDictionaryPath = String.Format(@"{0}/DataDictionary_{1:yyyyMMdd-Hmmss}.xml",
					                                         this.DataFolder,
					                                         DateTime.Now);
				}

				if (!File.Exists(this._dataDictionaryPath))
				{
					using (var file = File.CreateText(this._dataDictionaryPath))
					{
						file.Write(this.DataDictionary);
						file.Close();
					}
				}

				return this._dataDictionaryPath;
			}
		}

		public ImportRequest(BaseController controller)
			: base(controller.LISpMiner, controller.HttpContext)
		{
		}

		public ImportRequest(ApiBaseController controller)
			: base(controller.LISpMiner, new HttpContextWrapper(System.Web.HttpContext.Current))
		{
		}

		private string ReadDataDictionary()
		{
			var stream = this.HttpContext.Request.InputStream;

			using (var input = new StreamReader(stream))
			{
				stream.Position = 0;
				return input.ReadToEnd();
			}
		}
	}
}