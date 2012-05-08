﻿using System;
using System.Configuration;
using System.IO;
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

		#endregion

		protected static string GetAppSetting(string setting, string defaultValue)
		{
			var value = ConfigurationManager.AppSettings[setting];
			return String.IsNullOrEmpty(value) ? defaultValue : value;
		}

		protected static void RegisterExisting()
		{
			var env = MvcApplication.Environment;

			foreach (var path in Directory.GetDirectories(env.LMPoolPath))
			{
				try
				{
					var directory = new DirectoryInfo(path);
					var lm = new LISpMiner(directory, env);

					if (!env.Exists(lm.Id))
					{
						env.Register(lm);
					}
				}
				catch
				{
					continue;
				}
			}
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
			RegisterExisting();

			// Load logging info
			log4net.Config.XmlConfigurator.Configure();
		}

		protected void Application_Error(object sender, EventArgs e)
		{
			Log.Error(Server.GetLastError());
		}
	}
}