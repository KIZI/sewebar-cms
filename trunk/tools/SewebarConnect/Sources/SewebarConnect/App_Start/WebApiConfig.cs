using System.Web.Http;
using System.Web.Mvc;

namespace SewebarConnect
{
	public static class WebApiConfig
	{
		public static void Register(HttpConfiguration config)
		{
			// TODO: routes for canceling
			//config.Routes.MapHttpRoute(
			//	name: "MinersActionApi",
			//	routeTemplate: "miners/{minerId}/{controller}/{taskType}/{action}",
			//	defaults: new
			//	{
			//		controller = "Miners",
			//		minerId = UrlParameter.Optional,
			//		taskType = UrlParameter.Optional
			//	}
			//);

			config.Routes.MapHttpRoute(
				name: "MinersApi",
				routeTemplate: "miners/{minerId}/{controller}/{taskType}/{taskName}/{action}",
				defaults: new
					{
						controller = "Miners",
						minerId = UrlParameter.Optional,
						taskType = UrlParameter.Optional,
						taskName = UrlParameter.Optional,
						action = UrlParameter.Optional
					}
			);

			config.Routes.MapHttpRoute(
				name: "UsersApi",
				routeTemplate: "users/{username}",
				defaults: new
				{
					controller = "Users",
					id = UrlParameter.Optional
				}
			);

			config.Formatters.Remove(config.Formatters.JsonFormatter);
			config.Formatters.Remove(config.Formatters.XmlFormatter);
			config.Formatters.Add(new API.ResponseMediaTypeFormatter());
		}
	}
}
