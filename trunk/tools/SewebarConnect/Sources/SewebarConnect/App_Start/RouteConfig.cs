using System.Web.Mvc;
using System.Web.Routing;

namespace SewebarConnect
{
	public class RouteConfig
	{
		public static void RegisterRoutes(RouteCollection routes)
		{
			routes.IgnoreRoute("{resource}.axd/{*pathInfo}");

			routes.Add("TaskGen",
			           new OneWayRoute(url: "TaskGen/{controller}/{action}/{taskName}",
			                           defaults:
				                           new RouteValueDictionary(
				                           new {controller = "TaskPool", action = "Run", taskName = UrlParameter.Optional})));

			routes.MapRoute(
				name: "Default",
				url: "{controller}/{action}/{id}",
				defaults: new {controller = "Application", action = "Index", id = UrlParameter.Optional}
				);
		}
	}
}