using System;
using System.Web.Mvc;
using NHibernate;
using NHibernate.Context;

namespace SewebarConnect.Filters
{
	[AttributeUsage(AttributeTargets.Method, AllowMultiple = false)]
	public class NHibernateSessionAttribute : ActionFilterAttribute
	{
		protected ISessionFactory SessionFactory
		{
			get { return MvcApplication.SessionFactory; }
		}

		public override void OnActionExecuting(ActionExecutingContext filterContext)
		{
			ISession session = SessionFactory.OpenSession();
			CurrentSessionContext.Bind(session);
		}

		public override void OnResultExecuted(ResultExecutedContext filterContext)
		{
			ISession session = CurrentSessionContext.Unbind(SessionFactory);
			session.Close();
		}
	}
}