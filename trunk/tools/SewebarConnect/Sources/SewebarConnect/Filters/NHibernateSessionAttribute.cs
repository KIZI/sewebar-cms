using System;
using System.Web.Http.Controllers;
using System.Web.Http.Filters;
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

		public override void OnActionExecuting(HttpActionContext actionContext)
		{
			ISession session = SessionFactory.OpenSession();
			CurrentSessionContext.Bind(session);
		}

		public override void OnActionExecuted(HttpActionExecutedContext actionExecutedContext)
		{
			ISession session = CurrentSessionContext.Unbind(SessionFactory);
			session.Close();
		}
	}
}