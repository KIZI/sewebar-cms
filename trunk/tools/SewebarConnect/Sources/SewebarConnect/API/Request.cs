using System;
using System.IO;
using System.Web;
using LMWrapper.LISpMiner;
using SewebarConnect.Controllers;

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

		public virtual string UserName
		{
			get { return this.HttpContext.Request["username"]; }
		}

		public virtual string Password
		{
			get { return this.HttpContext.Request["password"]; }
		}

		public LISpMiner LISpMiner { get; private set; }

		public HttpContextBase HttpContext { get; private set; }

		public Request(LISpMiner miner, HttpContextBase httpContext)
			: this(httpContext)
		{
			if (miner != null)
			{
				this.DataFolder = String.Format("{0}/xml", miner.LMPrivatePath);

				this.LISpMiner = miner;
			}
		}

		public Request(ApiBaseController controller)
			: this(controller.LISpMiner, new HttpContextWrapper(System.Web.HttpContext.Current))
		{
		}

		public Request(HttpContextBase httpContext)
		{
			this.HttpContext = httpContext;
		}
	}
}