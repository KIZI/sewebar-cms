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
			if (miner != null)
			{
				this.DataFolder = String.Format("{0}/xml", miner.LMPath);

				this.LISpMiner = miner;
			}

			this.HttpContext = httpContext;
		}
	}
}