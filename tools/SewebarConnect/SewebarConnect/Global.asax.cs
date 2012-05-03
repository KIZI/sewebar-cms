using System;
using System.Collections.Generic;
using System.Configuration;
using System.Web.Configuration;
using System.Web.Mvc;
using System.Web.Routing;
using LMWrapper.LISpMiner;
using log4net;

namespace SewebarConnect
{
	public class MvcApplication : System.Web.HttpApplication
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(MvcApplication));
		private static LMWrapper.Environment _env;
		private static Dictionary<string, LISpMiner> _registeredSessionMiners;

		#region Properties

		public static LMWrapper.Environment Environment
		{
			get
			{
				if (_env == null)
				{
					_env = new LMWrapper.Environment
					{
						DataPath = GetAppSetting("Sewebar-DataPath", String.Format(@"{0}..\Data", AppDomain.CurrentDomain.BaseDirectory)),
						LMPoolPath = GetAppSetting("Sewebar-PoolPath", String.Format(@"{0}..\Data\LMs", AppDomain.CurrentDomain.BaseDirectory)),
						LMPath = GetAppSetting("Sewebar-LMPath",String.Format(@"{0}..\Libs\{1}", AppDomain.CurrentDomain.BaseDirectory, "LISp Miner")),
					};
				}

				return _env;
			}
		}

		protected static Dictionary<string, LISpMiner> RegisteredSessionMiners
		{
			get
			{
				if (_registeredSessionMiners == null)
				{
					_registeredSessionMiners = new Dictionary<string, LISpMiner>();
				}

				return _registeredSessionMiners;
			}
		}

		public static Dictionary<string, LISpMiner>.KeyCollection ExistingSessionMiners
		{
			get { return RegisteredSessionMiners.Keys; }
		}

		public static string RegisterSessionMiner(LISpMiner miner)
		{
			RegisteredSessionMiners.Add(miner.Id, miner);

			return miner.Id;
		}

		#endregion

		protected static string GetAppSetting(string setting, string defaultValue)
		{
			Configuration webconfig = WebConfigurationManager.OpenWebConfiguration(null);
			if (webconfig.AppSettings.Settings.Count > 0)
			{
				KeyValueConfigurationElement s = webconfig.AppSettings.Settings[setting];

				if (s != null && !string.IsNullOrEmpty(s.Value))
					return s.Value;
			}

			return defaultValue;
		}

		public static void RegisterGlobalFilters(GlobalFilterCollection filters)
		{
			filters.Add(new HandleErrorAttribute());
		}

		public static void RegisterRoutes(RouteCollection routes)
		{
			routes.IgnoreRoute("{resource}.axd/{*pathInfo}");

			routes.MapRoute(
				"Default", // Route name
				"{controller}/{action}/{id}", // URL with parameters
				new { controller = "Application", action = "Index", id = UrlParameter.Optional } // Parameter defaults
			);

		}

		protected void Application_Start()
		{
			AreaRegistration.RegisterAllAreas();

			RegisterGlobalFilters(GlobalFilters.Filters);
			RegisterRoutes(RouteTable.Routes);

			// Load logging info
			log4net.Config.XmlConfigurator.Configure();
		}

		protected void Application_Error(object sender, EventArgs e)
		{
			Log.Error(Server.GetLastError());
		}

		protected void Session_End(object sender, EventArgs e)
		{
			if (RegisteredSessionMiners.ContainsKey(Session.SessionID))
			{
				RegisteredSessionMiners.Remove(Session.SessionID);
			}

			//HttpHandlerSession.Clean(Session);
		}
	}
}