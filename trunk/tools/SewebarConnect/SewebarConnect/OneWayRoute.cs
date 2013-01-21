using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using System.Web.Routing;

namespace SewebarConnect
{
	public class OneWayRoute : Route
	{
		public OneWayRoute(string url, RouteValueDictionary defaults)
			: base(url, defaults, new MvcRouteHandler())
		{	
		}

		protected OneWayRoute(string url, IRouteHandler routeHandler) : base(url, routeHandler)
		{
		}

		protected OneWayRoute(string url, RouteValueDictionary defaults, IRouteHandler routeHandler) : base(url, defaults, routeHandler)
		{
		}

		protected OneWayRoute(string url, RouteValueDictionary defaults, RouteValueDictionary constraints, IRouteHandler routeHandler) : base(url, defaults, constraints, routeHandler)
		{
		}

		protected OneWayRoute(string url, RouteValueDictionary defaults, RouteValueDictionary constraints, RouteValueDictionary dataTokens, IRouteHandler routeHandler) : base(url, defaults, constraints, dataTokens, routeHandler)
		{
		}

		/// <summary>
		/// <seealso cref="http://stackoverflow.com/questions/1496740/asp-net-mvc-one-way-route"/>
		/// </summary>
		/// <param name="requestContext"></param>
		/// <param name="values"></param>
		/// <returns></returns>
		public override VirtualPathData GetVirtualPath(RequestContext requestContext, RouteValueDictionary values)
		{
			return null;
		}
	}
}