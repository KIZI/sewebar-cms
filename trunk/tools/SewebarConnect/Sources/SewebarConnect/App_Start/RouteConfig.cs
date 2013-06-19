using System.Web.Mvc;
using System.Web.Routing;

namespace SewebarConnect
{
	public class RouteConfig
	{
		public static void RegisterRoutes(RouteCollection routes)
		{
			routes.IgnoreRoute("{resource}.axd/{*pathInfo}");

			routes.MapRoute(
				name: "Default",
				url: "{controller}/{action}/{guid}",
				defaults: new { controller = "Application", action = "Index", guid = UrlParameter.Optional }
				);
		}
	}
}