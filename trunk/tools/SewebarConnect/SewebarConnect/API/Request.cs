using System;
using System.IO;
using System.Web;
using LMWrapper.LISpMiner;

namespace SewebarConnect.API
{
	public class Request
	{
		private string _dataFolder;

		public string DataFolder
		{
			get
			{
				if (!Directory.Exists(this._dataFolder))
				{
					Directory.CreateDirectory(this._dataFolder);
				}

				return _dataFolder;
			}
			private set { _dataFolder = value; }
		}

		public LISpMiner LISpMiner { get; private set; }

		public HttpContextBase HttpContext { get; private set; }

		public Request(LISpMiner miner, HttpContextBase httpContext)
		{
			this.DataFolder = String.Format("{1}/xml/{0}", miner != null ? miner.Id : String.Empty, AppDomain.CurrentDomain.GetData("DataDirectory"));

			this.LISpMiner = miner;
			this.HttpContext = httpContext;
		}
	}
}