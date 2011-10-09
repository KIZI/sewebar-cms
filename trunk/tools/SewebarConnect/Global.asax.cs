using System;
using System.Collections.Generic;
using LMWrapper.LISpMiner;
using LMWrapper.ODBC;
using log4net;

namespace SewebarWeb
{
	public class Global : System.Web.HttpApplication
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(Global));
		private static LMWrapper.Environment _env;

		#region Properties

		public static LMWrapper.Environment Environment
		{
			get
			{
				if(_env == null)
				{
					_env = new LMWrapper.Environment
					{
						DataPath = AppDomain.CurrentDomain.GetData("DataDirectory").ToString(),
						LMPoolPath = String.Format(@"{0}\Libs", System.AppDomain.CurrentDomain.BaseDirectory),
						LMPath = String.Format(@"{0}\Libs\{1}", System.AppDomain.CurrentDomain.BaseDirectory, "LISp Miner"),
					};
				}

				return _env;
			}
		}

		#endregion

		protected void Application_Start(object sender, EventArgs e)
		{
			// Load logging info
			log4net.Config.XmlConfigurator.Configure();
		}

		protected void Session_Start(object sender, EventArgs e)
		{
		
		}

		protected void Application_BeginRequest(object sender, EventArgs e)
		{

		}

		protected void Application_AuthenticateRequest(object sender, EventArgs e)
		{

		}

		protected void Application_Error(object sender, EventArgs e)
		{
			Log.Error(Server.GetLastError());
		}

		protected void Session_End(object sender, EventArgs e)
		{
			SessionBase.Clean(Session);
		}

		protected void Application_End(object sender, EventArgs e)
		{
			
		}
	}
}